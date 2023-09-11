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
use function count;
use function end;
use function explode;
use function implode;
use function in_array;
use function min;
use function reset;
use function sprintf;
use function strcasecmp;
use function strcmp;
use function uasort;
use const T_COMMA;
use const T_OPEN_TAG;
use const T_OPEN_USE_GROUP;
use const T_SEMICOLON;

class AlphabeticallySortedUsesSniff implements Sniff
{

	public const CODE_INCORRECT_ORDER = 'IncorrectlyOrderedUses';

	/** @var bool */
	public $psr12Compatible = true;

	/** @var bool */
	public $caseSensitive = false;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_OPEN_TAG,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $openTagPointer
	 */
	public function process(File $phpcsFile, $openTagPointer): void
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
								$useStatement->getFullyQualifiedTypeName()
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

		$commentsBefore = [];
		foreach ($useStatements as $useStatement) {
			$pointerBeforeUseStatement = TokenHelper::findPreviousNonWhitespace($phpcsFile, $useStatement->getPointer() - 1);

			if (!in_array($tokens[$pointerBeforeUseStatement]['code'], Tokens::$commentTokens, true)) {
				continue;
			}

			$commentAndWhitespace = TokenHelper::getContent($phpcsFile, $pointerBeforeUseStatement, $useStatement->getPointer() - 1);
			if (StringHelper::endsWith($commentAndWhitespace, $phpcsFile->eolChar . $phpcsFile->eolChar)) {
				continue;
			}

			$commentStartPointer = in_array($tokens[$pointerBeforeUseStatement]['code'], TokenHelper::$inlineCommentTokenCodes, true)
				? CommentHelper::getMultilineCommentStartPointer($phpcsFile, $pointerBeforeUseStatement)
				: $tokens[$pointerBeforeUseStatement]['comment_opener'];

			$commentsBefore[$useStatement->getPointer()] = TokenHelper::getContent(
				$phpcsFile,
				$commentStartPointer,
				$pointerBeforeUseStatement
			);

			if ($firstPointer === $useStatement->getPointer()) {
				$firstPointer = $commentStartPointer;
			}
		}

		uasort($useStatements, function (UseStatement $a, UseStatement $b): int {
			return $this->compareUseStatements($a, $b);
		});

		$phpcsFile->fixer->beginChangeset();

		FixerHelper::removeBetweenIncluding($phpcsFile, $firstPointer, $lastSemicolonPointer);

		$phpcsFile->fixer->addContent(
			$firstPointer,
			implode($phpcsFile->eolChar, array_map(static function (UseStatement $useStatement) use ($phpcsFile, $commentsBefore): string {
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
					$useStatement->getNameAsReferencedInFile()
				);
			}, $useStatements))
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

		$aNameParts = explode(NamespaceHelper::NAMESPACE_SEPARATOR, $a->getFullyQualifiedTypeName());
		$bNameParts = explode(NamespaceHelper::NAMESPACE_SEPARATOR, $b->getFullyQualifiedTypeName());

		$minPartsCount = min(count($aNameParts), count($bNameParts));
		for ($i = 0; $i < $minPartsCount; $i++) {
			$comparison = $this->compare($aNameParts[$i], $bNameParts[$i]);
			if ($comparison === 0) {
				continue;
			}

			return $comparison;
		}

		return count($aNameParts) <=> count($bNameParts);
	}

	private function compare(string $a, string $b): int
	{
		if ($this->caseSensitive) {
			return strcmp($a, $b);
		}

		return strcasecmp($a, $b);
	}

}
