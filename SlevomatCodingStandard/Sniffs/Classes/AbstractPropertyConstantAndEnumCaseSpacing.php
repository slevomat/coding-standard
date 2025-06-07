<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\ClassHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function assert;
use function in_array;
use function max;
use function str_repeat;
use const T_ATTRIBUTE;
use const T_COMMENT;
use const T_CONST;
use const T_DOC_COMMENT_OPEN_TAG;
use const T_ENUM_CASE;
use const T_FUNCTION;
use const T_OPEN_CURLY_BRACKET;
use const T_SEMICOLON;
use const T_USE;
use const T_VARIABLE;

/**
 * @internal
 */
abstract class AbstractPropertyConstantAndEnumCaseSpacing implements Sniff
{

	public int $minLinesCountBeforeWithComment = 1;

	public int $maxLinesCountBeforeWithComment = 1;

	public int $minLinesCountBeforeWithoutComment = 0;

	public int $maxLinesCountBeforeWithoutComment = 1;

	public ?int $minLinesCountBeforeMultiline = null;

	public ?int $maxLinesCountBeforeMultiline = null;

	abstract protected function isNextMemberValid(File $phpcsFile, int $pointer): bool;

	abstract protected function addError(File $phpcsFile, int $pointer, int $min, int $max, int $found): bool;

	public function process(File $phpcsFile, int $pointer): int
	{
		$this->minLinesCountBeforeWithComment = SniffSettingsHelper::normalizeInteger($this->minLinesCountBeforeWithComment);
		$this->maxLinesCountBeforeWithComment = SniffSettingsHelper::normalizeInteger($this->maxLinesCountBeforeWithComment);
		$this->minLinesCountBeforeWithoutComment = SniffSettingsHelper::normalizeInteger($this->minLinesCountBeforeWithoutComment);
		$this->maxLinesCountBeforeWithoutComment = SniffSettingsHelper::normalizeInteger($this->maxLinesCountBeforeWithoutComment);
		$this->minLinesCountBeforeMultiline = SniffSettingsHelper::normalizeNullableInteger($this->minLinesCountBeforeMultiline);
		$this->maxLinesCountBeforeMultiline = SniffSettingsHelper::normalizeNullableInteger($this->maxLinesCountBeforeMultiline);

		$tokens = $phpcsFile->getTokens();

		$classPointer = ClassHelper::getClassPointer($phpcsFile, $pointer);

		$endPointer = $this->getEndPointer($phpcsFile, $pointer);

		$firstOnLinePointer = TokenHelper::findFirstTokenOnNextLine($phpcsFile, $endPointer);
		assert($firstOnLinePointer !== null);

		$nextFunctionPointer = TokenHelper::findNext(
			$phpcsFile,
			[T_FUNCTION, T_ENUM_CASE, T_CONST, T_VARIABLE, T_USE],
			$firstOnLinePointer + 1,
		);
		if (
			$nextFunctionPointer === null
			|| $tokens[$nextFunctionPointer]['code'] === T_FUNCTION
			|| $tokens[$nextFunctionPointer]['conditions'] !== $tokens[$pointer]['conditions']
		) {
			return $nextFunctionPointer ?? $firstOnLinePointer;
		}

		$types = [T_COMMENT, T_DOC_COMMENT_OPEN_TAG, T_ATTRIBUTE, T_ENUM_CASE, T_CONST, T_USE, ...TokenHelper::PROPERTY_MODIFIERS_TOKEN_CODES];
		$nextPointer = TokenHelper::findNext($phpcsFile, $types, $firstOnLinePointer + 1, $tokens[$classPointer]['scope_closer']);

		if (!$this->isNextMemberValid($phpcsFile, $nextPointer)) {
			return $nextPointer;
		}

		$linesBetween = $tokens[$nextPointer]['line'] - $tokens[$endPointer]['line'] - 1;
		if (in_array($tokens[$nextPointer]['code'], [T_DOC_COMMENT_OPEN_TAG, T_COMMENT, T_ATTRIBUTE], true)) {
			$minExpectedLines = $this->minLinesCountBeforeWithComment;
			$maxExpectedLines = $this->maxLinesCountBeforeWithComment;
		} else {
			$minExpectedLines = $this->minLinesCountBeforeWithoutComment;
			$maxExpectedLines = $this->maxLinesCountBeforeWithoutComment;
		}

		if (
			$this->minLinesCountBeforeMultiline !== null
			&& !$this instanceof EnumCaseSpacingSniff
			&& $tokens[$pointer]['line'] !== $tokens[$endPointer]['line']
		) {
			$minExpectedLines = max($minExpectedLines, $this->minLinesCountBeforeMultiline);
			$maxExpectedLines = max($minExpectedLines, $maxExpectedLines);
		}

		if (
			$this->maxLinesCountBeforeMultiline !== null
			&& !$this instanceof EnumCaseSpacingSniff
			&& $tokens[$pointer]['line'] !== $tokens[$endPointer]['line']
		) {
			$maxExpectedLines = max($minExpectedLines, $this->maxLinesCountBeforeMultiline);
		}

		if ($linesBetween >= $minExpectedLines && $linesBetween <= $maxExpectedLines) {
			return $firstOnLinePointer;
		}

		$fix = $this->addError($phpcsFile, $pointer, $minExpectedLines, $maxExpectedLines, $linesBetween);
		if (!$fix) {
			return $firstOnLinePointer;
		}

		if ($linesBetween > $maxExpectedLines) {
			$lastPointerOnLine = TokenHelper::findLastTokenOnLine($phpcsFile, $endPointer);
			$firstPointerOnNextLine = TokenHelper::findFirstTokenOnLine($phpcsFile, $nextPointer);

			$phpcsFile->fixer->beginChangeset();

			if ($maxExpectedLines > 0) {
				FixerHelper::add(
					$phpcsFile,
					$lastPointerOnLine,
					str_repeat($phpcsFile->eolChar, $maxExpectedLines),
				);
			}

			FixerHelper::removeBetween($phpcsFile, $lastPointerOnLine, $firstPointerOnNextLine);

			$phpcsFile->fixer->endChangeset();
		} elseif ($linesBetween < $minExpectedLines) {
			$phpcsFile->fixer->beginChangeset();

			for ($i = 0; $i < $minExpectedLines - $linesBetween; $i++) {
				$phpcsFile->fixer->addNewlineBefore($firstOnLinePointer);
			}

			$phpcsFile->fixer->endChangeset();
		}

		return $firstOnLinePointer;
	}

	private function getEndPointer(File $phpcsFile, int $pointer): int
	{
		$tokens = $phpcsFile->getTokens();

		$endPointer = TokenHelper::findNext($phpcsFile, [T_SEMICOLON, T_OPEN_CURLY_BRACKET], $pointer + 1);

		return $tokens[$endPointer]['code'] === T_OPEN_CURLY_BRACKET
			? $tokens[$endPointer]['bracket_closer']
			: $endPointer;
	}

}
