<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use SlevomatCodingStandard\Helpers\CommentHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\StringHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\UseStatement;
use SlevomatCodingStandard\Helpers\UseStatementHelper;
use function array_key_exists;
use function array_map;
use function end;
use function implode;
use function in_array;
use function reset;
use function sprintf;
use function uasort;
use const T_COMMA;
use const T_DOC_COMMENT_OPEN_TAG;
use const T_OPEN_TAG;
use const T_OPEN_USE_GROUP;
use const T_SEMICOLON;

class AlphabeticallySortedUsesSniff implements Sniff
{

	public const CODE_INCORRECT_ORDER = 'IncorrectlyOrderedUses';

	public bool $psr12Compatible = true;

	public bool $caseSensitive = false;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_OPEN_TAG,
		];
	}

	public function process(File $phpcsFile, int $openTagPointer): void
	{
		if (TokenHelper::findPrevious($phpcsFile, T_OPEN_TAG, $openTagPointer - 1) !== null) {
			return;
		}

		// If there are any 'use group' statements then we cannot sort and fix the file.
		$groupUsePointer = TokenHelper::findNext($phpcsFile, T_OPEN_USE_GROUP, $openTagPointer);
		if ($groupUsePointer !== null) {
			return;
		}

		$fileUseStatements = UseStatementHelper::getFileUseStatements($phpcsFile);
		foreach ($fileUseStatements as $useStatements) {
			$lastUse = null;
			foreach ($useStatements as $useStatement) {
				if ($lastUse === null) {
					$lastUse = $useStatement;
				} else {
					$order = $this->compareUseStatements($useStatement, $lastUse);
					if ($order < 0) {
						// The use statements are not ordered correctly. Go through all statements and if any are multi-part then
						// we report the problem but cannot fix it, because this would lose the secondary parts of the statement.
						$fixable = true;
						$tokens = $phpcsFile->getTokens();
						foreach ($useStatements as $statement) {
							$nextBreaker = TokenHelper::findNext($phpcsFile, [T_SEMICOLON, T_COMMA], $statement->getPointer());

							if ($tokens[$nextBreaker]['code'] === T_COMMA) {
								$fixable = false;
								break;
							}
						}

						$errorParameters = [
							sprintf(
								'Use statements should be sorted alphabetically. The first wrong one is %s.',
								$useStatement->getFullyQualifiedTypeName(),
							),
							$useStatement->getPointer(),
							self::CODE_INCORRECT_ORDER,
						];

						if (!$fixable) {
							$phpcsFile->addError(...$errorParameters);
							return;
						}

						$fix = $phpcsFile->addFixableError(...$errorParameters);
						if ($fix) {
							$this->fixAlphabeticalOrder($phpcsFile, $useStatements);
						}

						return;
					}

					$lastUse = $useStatement;
				}
			}
		}
	}

	/**
	 * @param array<string, UseStatement> $useStatements
	 */
	private function fixAlphabeticalOrder(File $phpcsFile, array $useStatements): void
	{
		/** @var UseStatement $firstUseStatement */
		$firstUseStatement = reset($useStatements);
		/** @var UseStatement $lastUseStatement */
		$lastUseStatement = end($useStatements);
		$lastSemicolonPointer = TokenHelper::findNext($phpcsFile, T_SEMICOLON, $lastUseStatement->getPointer());

		$firstPointer = $firstUseStatement->getPointer();

		$tokens = $phpcsFile->getTokens();

		// Track comments before use statements
		$commentsBefore = [];
		// Track potential block-level comment (docblock before first use when only first has one)
		$blockLevelComment = null;
		$firstUseDocblockInfo = null;

		// First pass: collect all comments and detect if first use has a docblock
		foreach ($useStatements as $useStatement) {
			$pointerBeforeUseStatement = TokenHelper::findPreviousNonWhitespace($phpcsFile, $useStatement->getPointer() - 1);

			if (!in_array($tokens[$pointerBeforeUseStatement]['code'], Tokens::COMMENT_TOKENS, true)) {
				continue;
			}

			$commentAndWhitespace = TokenHelper::getContent($phpcsFile, $pointerBeforeUseStatement, $useStatement->getPointer() - 1);
			if (StringHelper::endsWith($commentAndWhitespace, $phpcsFile->eolChar . $phpcsFile->eolChar)) {
				continue;
			}

			$commentStartPointer = in_array($tokens[$pointerBeforeUseStatement]['code'], TokenHelper::INLINE_COMMENT_TOKEN_CODES, true)
				? CommentHelper::getMultilineCommentStartPointer($phpcsFile, $pointerBeforeUseStatement)
				: $tokens[$pointerBeforeUseStatement]['comment_opener'];

			if ($firstPointer === $useStatement->getPointer()) {
				$firstPointer = $commentStartPointer;
				$isDocblock = $tokens[$commentStartPointer]['code'] === T_DOC_COMMENT_OPEN_TAG;
				if ($isDocblock) {
					// Save info for second pass - we may treat this as block-level
					$firstUseDocblockInfo = [
						'startPointer' => $commentStartPointer,
						'endPointer' => $pointerBeforeUseStatement,
						'usePointer' => $useStatement->getPointer(),
					];
					continue;
				}
			}

			$commentsBefore[$useStatement->getPointer()] = TokenHelper::getContent(
				$phpcsFile,
				$commentStartPointer,
				$pointerBeforeUseStatement,
			);
		}

		// If first use has a docblock and no other uses have comments, treat it as block-level
		// (likely file-level documentation). Otherwise, it's per-use and should move with sorting.
		if ($firstUseDocblockInfo !== null) {
			if (count($commentsBefore) === 0) {
				// Only first use has a comment - treat as block-level
				$blockLevelComment = TokenHelper::getContent(
					$phpcsFile,
					$firstUseDocblockInfo['startPointer'],
					$firstUseDocblockInfo['endPointer'],
				);
			} else {
				// Other uses also have comments - treat first use's docblock as per-use
				$commentsBefore[$firstUseDocblockInfo['usePointer']] = TokenHelper::getContent(
					$phpcsFile,
					$firstUseDocblockInfo['startPointer'],
					$firstUseDocblockInfo['endPointer'],
				);
			}
		}

		uasort($useStatements, fn (UseStatement $a, UseStatement $b): int => $this->compareUseStatements($a, $b));

		$phpcsFile->fixer->beginChangeset();

		FixerHelper::removeBetweenIncluding($phpcsFile, $firstPointer, $lastSemicolonPointer);

		// Build the new content with block-level comment first, then sorted uses
		$blockLevelCommentContent = '';
		if ($blockLevelComment !== null) {
			$blockLevelCommentContent = $blockLevelComment;
			if (!StringHelper::endsWith($blockLevelCommentContent, $phpcsFile->eolChar)) {
				$blockLevelCommentContent .= $phpcsFile->eolChar;
			}
		}

		FixerHelper::add(
			$phpcsFile,
			$firstPointer,
			$blockLevelCommentContent . implode($phpcsFile->eolChar, array_map(static function (UseStatement $useStatement) use ($phpcsFile, $commentsBefore): string {
				$unqualifiedName = NamespaceHelper::getUnqualifiedNameFromFullyQualifiedName($useStatement->getFullyQualifiedTypeName());

				$useTypeName = UseStatement::getTypeName($useStatement->getType());
				$useTypeFormatted = $useTypeName !== null ? sprintf('%s ', $useTypeName) : '';

				$commentBefore = '';
				if (array_key_exists($useStatement->getPointer(), $commentsBefore)) {
					$commentBefore = $commentsBefore[$useStatement->getPointer()];
					if (!StringHelper::endsWith($commentBefore, $phpcsFile->eolChar)) {
						$commentBefore .= $phpcsFile->eolChar;
					}
				}

				if ($unqualifiedName === $useStatement->getNameAsReferencedInFile()) {
					return sprintf('%suse %s%s;', $commentBefore, $useTypeFormatted, $useStatement->getFullyQualifiedTypeName());
				}

				return sprintf(
					'%suse %s%s as %s;',
					$commentBefore,
					$useTypeFormatted,
					$useStatement->getFullyQualifiedTypeName(),
					$useStatement->getNameAsReferencedInFile(),
				);
			}, $useStatements)),
		);
		$phpcsFile->fixer->endChangeset();
	}

	private function compareUseStatements(UseStatement $a, UseStatement $b): int
	{
		if (!$a->hasSameType($b)) {
			$order = [
				UseStatement::TYPE_CLASS => 1,
				UseStatement::TYPE_FUNCTION => $this->psr12Compatible ? 2 : 3,
				UseStatement::TYPE_CONSTANT => $this->psr12Compatible ? 3 : 2,
			];

			return $order[$a->getType()] <=> $order[$b->getType()];
		}

		return NamespaceHelper::compareStatements($a->getFullyQualifiedTypeName(), $b->getFullyQualifiedTypeName(), $this->caseSensitive);
	}

}
