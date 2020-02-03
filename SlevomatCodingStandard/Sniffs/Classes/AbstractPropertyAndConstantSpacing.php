<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function assert;
use function in_array;
use const T_COMMENT;
use const T_CONST;
use const T_DOC_COMMENT_OPEN_TAG;
use const T_FUNCTION;
use const T_PRIVATE;
use const T_PROTECTED;
use const T_PUBLIC;
use const T_SEMICOLON;
use const T_VAR;
use const T_VARIABLE;

/**
 * @internal
 */
abstract class AbstractPropertyAndConstantSpacing implements Sniff
{

	/** @var int */
	public $minLinesCountBeforeWithComment = 1;

	/** @var int */
	public $maxLinesCountBeforeWithComment = 1;

	/** @var int */
	public $minLinesCountBeforeWithoutComment = 0;

	/** @var int */
	public $maxLinesCountBeforeWithoutComment = 1;

	abstract protected function isNextMemberValid(File $phpcsFile, int $pointer): bool;

	abstract protected function addError(File $phpcsFile, int $pointer, int $min, int $max, int $found): bool;

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param File $phpcsFile
	 * @param int $pointer
	 */
	public function process(File $phpcsFile, $pointer): int
	{
		$tokens = $phpcsFile->getTokens();

		$nextSemicolon = TokenHelper::findNext($phpcsFile, [T_SEMICOLON], $pointer + 1);
		assert($nextSemicolon !== null);

		$firstOnLinePointer = TokenHelper::findFirstTokenOnNextLine($phpcsFile, $nextSemicolon);
		assert($firstOnLinePointer !== null);

		$nextFunctionPointer = TokenHelper::findNext($phpcsFile, [T_FUNCTION, T_CONST, T_VARIABLE], $firstOnLinePointer + 1);
		if ($nextFunctionPointer === null || $tokens[$nextFunctionPointer]['code'] === T_FUNCTION || $tokens[$nextFunctionPointer]['conditions'] !== $tokens[$pointer]['conditions']) {
			return $nextFunctionPointer ?? $firstOnLinePointer;
		}

		$types = [T_COMMENT, T_DOC_COMMENT_OPEN_TAG, T_CONST, T_VAR, T_PUBLIC, T_PROTECTED, T_PRIVATE];
		$nextPointer = TokenHelper::findNext($phpcsFile, $types, $firstOnLinePointer + 1);

		if (!$this->isNextMemberValid($phpcsFile, $nextPointer)) {
			return $nextPointer;
		}

		$linesBetween = $tokens[$nextPointer]['line'] - $tokens[$nextSemicolon]['line'] - 1;
		if (in_array($tokens[$nextPointer]['code'], [T_DOC_COMMENT_OPEN_TAG, T_COMMENT], true)) {
			$minExpectedLines = SniffSettingsHelper::normalizeInteger($this->minLinesCountBeforeWithComment);
			$maxExpectedLines = SniffSettingsHelper::normalizeInteger($this->maxLinesCountBeforeWithComment);
		} else {
			$minExpectedLines = SniffSettingsHelper::normalizeInteger($this->minLinesCountBeforeWithoutComment);
			$maxExpectedLines = SniffSettingsHelper::normalizeInteger($this->maxLinesCountBeforeWithoutComment);
		}

		if ($linesBetween >= $minExpectedLines && $linesBetween <= $maxExpectedLines) {
			return $firstOnLinePointer;
		}

		$fix = $this->addError($phpcsFile, $pointer, $minExpectedLines, $maxExpectedLines, $linesBetween);
		if (!$fix) {
			return $firstOnLinePointer;
		}

		$phpcsFile->fixer->beginChangeset();

		if ($linesBetween > $maxExpectedLines) {
			for ($i = $firstOnLinePointer; $i < $firstOnLinePointer + $maxExpectedLines; $i++) {
				$phpcsFile->fixer->replaceToken($i, '');
			}
		} else {
			for ($i = 0; $i < $minExpectedLines; $i++) {
				$phpcsFile->fixer->addNewlineBefore($firstOnLinePointer);
			}
		}

		$phpcsFile->fixer->endChangeset();

		return $firstOnLinePointer;
	}

}
