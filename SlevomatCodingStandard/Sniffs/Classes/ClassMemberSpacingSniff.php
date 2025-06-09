<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use SlevomatCodingStandard\Helpers\CommentHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\PropertyHelper;
use SlevomatCodingStandard\Helpers\ScopeHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\StringHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\UseStatementHelper;
use function array_key_exists;
use function in_array;
use function sprintf;
use function str_repeat;
use const T_AS;
use const T_ATTRIBUTE_END;
use const T_CLOSE_CURLY_BRACKET;
use const T_CONST;
use const T_ENUM_CASE;
use const T_FUNCTION;
use const T_OPEN_CURLY_BRACKET;
use const T_SEMICOLON;
use const T_USE;
use const T_VARIABLE;

class ClassMemberSpacingSniff implements Sniff
{

	public const CODE_INCORRECT_COUNT_OF_BLANK_LINES_BETWEEN_MEMBERS = 'IncorrectCountOfBlankLinesBetweenMembers';

	public int $linesCountBetweenMembers = 1;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return TokenHelper::CLASS_TYPE_WITH_ANONYMOUS_CLASS_TOKEN_CODES;
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $classPointer
	 */
	public function process(File $phpcsFile, $classPointer): void
	{
		$this->linesCountBetweenMembers = SniffSettingsHelper::normalizeInteger($this->linesCountBetweenMembers);

		$tokens = $phpcsFile->getTokens();

		$memberPointer = null;

		do {
			$previousMemberPointer = $memberPointer;

			$memberPointer = $this->findNextMember(
				$phpcsFile,
				$classPointer,
				$previousMemberPointer ?? $tokens[$classPointer]['scope_opener'],
			);

			if ($memberPointer === null) {
				break;
			}

			if ($previousMemberPointer === null) {
				continue;
			}

			if ($tokens[$previousMemberPointer]['code'] === $tokens[$memberPointer]['code']) {
				continue;
			}

			$previousMemberEndPointer = $this->getMemberEndPointer($phpcsFile, $previousMemberPointer);

			$hasCommentWithNewLineAfterPreviousMember = false;

			$commentPointerAfterPreviousMember = TokenHelper::findNextNonWhitespace($phpcsFile, $previousMemberEndPointer + 1);
			if (
				in_array($tokens[$commentPointerAfterPreviousMember]['code'], TokenHelper::INLINE_COMMENT_TOKEN_CODES, true)
				&& (
					$tokens[$previousMemberEndPointer]['line'] === $tokens[$commentPointerAfterPreviousMember]['line']
					|| $tokens[$previousMemberEndPointer]['line'] + 1 === $tokens[$commentPointerAfterPreviousMember]['line']
				)
			) {
				$previousMemberEndPointer = CommentHelper::getCommentEndPointer($phpcsFile, $commentPointerAfterPreviousMember);

				if (StringHelper::endsWith($tokens[$commentPointerAfterPreviousMember]['content'], $phpcsFile->eolChar)) {
					$hasCommentWithNewLineAfterPreviousMember = true;
				}
			}

			$memberStartPointer = $this->getMemberStartPointer($phpcsFile, $memberPointer, $previousMemberEndPointer);

			$actualLinesCount = $tokens[$memberStartPointer]['line'] - $tokens[$previousMemberEndPointer]['line'] - 1;

			if ($actualLinesCount === $this->linesCountBetweenMembers) {
				continue;
			}

			$errorMessage = $this->linesCountBetweenMembers === 1
				? 'Expected 1 blank line between class members, found %2$d.'
				: 'Expected %1$d blank lines between class members, found %2$d.';

			$firstPointerOnMemberLine = TokenHelper::findFirstTokenOnLine($phpcsFile, $memberStartPointer);
			$nonWhitespaceBetweenMembersPointer = TokenHelper::findNextNonWhitespace(
				$phpcsFile,
				$previousMemberEndPointer + 1,
				$firstPointerOnMemberLine,
			);
			$errorParameters = [
				sprintf($errorMessage, $this->linesCountBetweenMembers, $actualLinesCount),
				$memberPointer,
				self::CODE_INCORRECT_COUNT_OF_BLANK_LINES_BETWEEN_MEMBERS,
			];

			if ($nonWhitespaceBetweenMembersPointer !== null) {
				$phpcsFile->addError(...$errorParameters);
				continue;
			}

			$fix = $phpcsFile->addFixableError(...$errorParameters);
			if (!$fix) {
				continue;
			}

			$newLines = str_repeat(
				$phpcsFile->eolChar,
				$this->linesCountBetweenMembers + ($hasCommentWithNewLineAfterPreviousMember ? 0 : 1),
			);

			$phpcsFile->fixer->beginChangeset();

			FixerHelper::add($phpcsFile, $previousMemberEndPointer, $newLines);

			FixerHelper::removeBetween($phpcsFile, $previousMemberEndPointer, $firstPointerOnMemberLine);

			$phpcsFile->fixer->endChangeset();

		} while (true);
	}

	private function findNextMember(File $phpcsFile, int $classPointer, int $previousMemberPointer): ?int
	{
		$tokens = $phpcsFile->getTokens();

		$memberTokenCodes = [T_USE, T_CONST, T_FUNCTION, T_ENUM_CASE, ...TokenHelper::PROPERTY_MODIFIERS_TOKEN_CODES];

		$memberPointer = $previousMemberPointer;
		do {
			$memberPointer = TokenHelper::findNext(
				$phpcsFile,
				$memberTokenCodes,
				$memberPointer + 1,
				$tokens[$classPointer]['scope_closer'],
			);

			if ($memberPointer === null) {
				return null;
			}

			if ($tokens[$memberPointer]['code'] === T_USE) {
				if (!UseStatementHelper::isTraitUse($phpcsFile, $memberPointer)) {
					continue;
				}
			} elseif (in_array($tokens[$memberPointer]['code'], TokenHelper::PROPERTY_MODIFIERS_TOKEN_CODES, true)) {
				$asPointer = TokenHelper::findPreviousEffective($phpcsFile, $memberPointer - 1);
				if ($tokens[$asPointer]['code'] === T_AS) {
					continue;
				}

				$propertyPointer = TokenHelper::findNext($phpcsFile, [T_VARIABLE, T_FUNCTION, T_CONST], $memberPointer + 1);
				if (
					$propertyPointer === null
					|| $tokens[$propertyPointer]['code'] !== T_VARIABLE
					|| !PropertyHelper::isProperty($phpcsFile, $propertyPointer)
				) {
					continue;
				}

				$memberPointer = $propertyPointer;
			}

			if (ScopeHelper::isInSameScope($phpcsFile, $memberPointer, $previousMemberPointer)) {
				break;
			}

		} while (true);

		return $memberPointer;
	}

	private function getMemberStartPointer(File $phpcsFile, int $memberPointer, int $previousMemberEndPointer): int
	{
		$tokens = $phpcsFile->getTokens();

		$memberFirstCodePointer = $this->getMemberFirstCodePointer($phpcsFile, $memberPointer);

		do {
			if ($memberFirstCodePointer <= $previousMemberEndPointer) {
				return TokenHelper::findNextNonWhitespace($phpcsFile, $memberFirstCodePointer + 1);
			}

			$pointerBefore = TokenHelper::findPreviousNonWhitespace($phpcsFile, $memberFirstCodePointer - 1);

			if ($tokens[$pointerBefore]['code'] === T_ATTRIBUTE_END) {
				$memberFirstCodePointer = $tokens[$pointerBefore]['attribute_opener'];
				continue;
			}

			if (in_array($tokens[$pointerBefore]['code'], Tokens::$commentTokens, true)) {
				$pointerBeforeComment = TokenHelper::findPreviousEffective($phpcsFile, $pointerBefore - 1);
				if ($tokens[$pointerBeforeComment]['line'] !== $tokens[$pointerBefore]['line']) {
					$memberFirstCodePointer = array_key_exists('comment_opener', $tokens[$pointerBefore])
						? $tokens[$pointerBefore]['comment_opener']
						: CommentHelper::getMultilineCommentStartPointer($phpcsFile, $pointerBefore);
					continue;
				}
			}

			break;

		} while (true);

		return $memberFirstCodePointer;
	}

	private function getMemberFirstCodePointer(File $phpcsFile, int $memberPointer): int
	{
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$memberPointer]['code'] === T_USE) {
			return $memberPointer;
		}

		$endTokenCodes = [T_SEMICOLON, T_CLOSE_CURLY_BRACKET];
		$startOrEndTokenCodes = [...TokenHelper::MODIFIERS_TOKEN_CODES, ...$endTokenCodes];

		$firstCodePointer = $memberPointer;
		$previousFirstCodePointer = $memberPointer;
		do {
			/** @var int $firstCodePointer */
			$firstCodePointer = TokenHelper::findPrevious($phpcsFile, $startOrEndTokenCodes, $firstCodePointer - 1);

			if (in_array($tokens[$firstCodePointer]['code'], $endTokenCodes, true)) {
				break;
			}

			$previousFirstCodePointer = $firstCodePointer;

		} while (true);

		return $previousFirstCodePointer;
	}

	private function getMemberEndPointer(File $phpcsFile, int $memberPointer): int
	{
		$tokens = $phpcsFile->getTokens();

		if (
			$tokens[$memberPointer]['code'] === T_USE
			// Property with hooks
			|| $tokens[$memberPointer]['code'] === T_VARIABLE
		) {
			$pointer = TokenHelper::findNextLocal($phpcsFile, [T_SEMICOLON, T_OPEN_CURLY_BRACKET], $memberPointer + 1);

			return $tokens[$pointer]['code'] === T_OPEN_CURLY_BRACKET
				? $tokens[$pointer]['bracket_closer']
				: $pointer;
		}

		if ($tokens[$memberPointer]['code'] === T_FUNCTION && !FunctionHelper::isAbstract($phpcsFile, $memberPointer)) {
			return $tokens[$memberPointer]['scope_closer'];
		}

		return TokenHelper::findNext($phpcsFile, T_SEMICOLON, $memberPointer + 1);
	}

}
