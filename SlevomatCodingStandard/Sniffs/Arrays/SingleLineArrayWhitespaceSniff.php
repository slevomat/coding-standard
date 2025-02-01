<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Arrays;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\ArrayHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function in_array;
use function sprintf;
use function str_repeat;
use const T_COMMA;
use const T_OPEN_PARENTHESIS;
use const T_WHITESPACE;

class SingleLineArrayWhitespaceSniff implements Sniff
{

	public const CODE_SPACE_BEFORE_COMMA = 'SpaceBeforeComma';
	public const CODE_SPACE_AFTER_COMMA = 'SpaceAfterComma';
	public const CODE_SPACE_AFTER_ARRAY_OPEN = 'SpaceAfterArrayOpen';
	public const CODE_SPACE_BEFORE_ARRAY_CLOSE = 'SpaceBeforeArrayClose';
	public const CODE_SPACE_IN_EMPTY_ARRAY = 'SpaceInEmptyArray';

	public int $spacesAroundBrackets = 0;

	public bool $enableEmptyArrayCheck = false;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return TokenHelper::$arrayTokenCodes;
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $stackPointer
	 */
	public function process(File $phpcsFile, $stackPointer): int
	{
		$this->spacesAroundBrackets = SniffSettingsHelper::normalizeInteger($this->spacesAroundBrackets);

		$tokens = $phpcsFile->getTokens();

		[$arrayOpenerPointer, $arrayCloserPointer] = ArrayHelper::openClosePointers($tokens[$stackPointer]);

		// Check only single-line arrays.
		if ($tokens[$arrayOpenerPointer]['line'] !== $tokens[$arrayCloserPointer]['line']) {
			return $arrayCloserPointer;
		}

		$pointerContent = TokenHelper::findNextNonWhitespace($phpcsFile, $arrayOpenerPointer + 1, $arrayCloserPointer + 1);
		if ($pointerContent === $arrayCloserPointer) {
			// Empty array, but if the brackets aren't together, there's a problem.
			if ($this->enableEmptyArrayCheck) {
				$this->checkWhitespaceInEmptyArray($phpcsFile, $arrayOpenerPointer, $arrayCloserPointer);
			}

			// We can return here because there is nothing else to check.
			// All code below can assume that the array is not empty.
			return $arrayCloserPointer + 1;
		}

		$this->checkWhitespaceAfterOpeningBracket($phpcsFile, $arrayOpenerPointer);
		$this->checkWhitespaceBeforeClosingBracket($phpcsFile, $arrayCloserPointer);

		for ($i = $arrayOpenerPointer + 1; $i < $arrayCloserPointer; $i++) {
			// Skip bracketed statements, like function calls.
			if ($tokens[$i]['code'] === T_OPEN_PARENTHESIS) {
				$i = $tokens[$i]['parenthesis_closer'];

				continue;
			}

			// Skip nested arrays as they will be processed separately
			if (in_array($tokens[$i]['code'], TokenHelper::$arrayTokenCodes, true)) {
				$i = ArrayHelper::openClosePointers($tokens[$i])[1];

				continue;
			}

			if ($tokens[$i]['code'] !== T_COMMA) {
				continue;
			}

			// Before checking this comma, make sure we are not at the end of the array.
			$next = TokenHelper::findNextNonWhitespace($phpcsFile, $i + 1, $arrayCloserPointer);
			if ($next === null) {
				return $arrayOpenerPointer + 1;
			}

			$this->checkWhitespaceBeforeComma($phpcsFile, $i);
			$this->checkWhitespaceAfterComma($phpcsFile, $i);
		}

		return $arrayOpenerPointer + 1;
	}

	private function checkWhitespaceInEmptyArray(File $phpcsFile, int $arrayStart, int $arrayEnd): void
	{
		if ($arrayEnd - $arrayStart === 1) {
			return;
		}

		$error = 'Empty array declaration must have no space between the parentheses.';
		$fix = $phpcsFile->addFixableError($error, $arrayStart, self::CODE_SPACE_IN_EMPTY_ARRAY);
		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->replaceToken($arrayStart + 1, '');
	}

	private function checkWhitespaceAfterOpeningBracket(File $phpcsFile, int $arrayStart): void
	{
		$tokens = $phpcsFile->getTokens();

		$whitespacePointer = $arrayStart + 1;

		$spaceLength = 0;
		if ($tokens[$whitespacePointer]['code'] === T_WHITESPACE) {
			$spaceLength = $tokens[$whitespacePointer]['length'];
		}

		if ($spaceLength === $this->spacesAroundBrackets) {
			return;
		}

		$error = sprintf('Expected %d spaces after array opening bracket, %d found.', $this->spacesAroundBrackets, $spaceLength);
		$fix = $phpcsFile->addFixableError($error, $arrayStart, self::CODE_SPACE_AFTER_ARRAY_OPEN);
		if (!$fix) {
			return;
		}

		if ($spaceLength === 0) {
			$phpcsFile->fixer->addContent($arrayStart, str_repeat(' ', $this->spacesAroundBrackets));
		} else {
			$phpcsFile->fixer->replaceToken($whitespacePointer, str_repeat(' ', $this->spacesAroundBrackets));
		}
	}

	private function checkWhitespaceBeforeClosingBracket(File $phpcsFile, int $arrayEnd): void
	{
		$tokens = $phpcsFile->getTokens();

		$whitespacePointer = $arrayEnd - 1;

		$spaceLength = 0;
		if ($tokens[$whitespacePointer]['code'] === T_WHITESPACE) {
			$spaceLength = $tokens[$whitespacePointer]['length'];
		}

		if ($spaceLength === $this->spacesAroundBrackets) {
			return;
		}

		$error = sprintf('Expected %d spaces before array closing bracket, %d found.', $this->spacesAroundBrackets, $spaceLength);
		$fix = $phpcsFile->addFixableError($error, $arrayEnd, self::CODE_SPACE_BEFORE_ARRAY_CLOSE);
		if (!$fix) {
			return;
		}

		if ($spaceLength === 0) {
			$phpcsFile->fixer->addContentBefore($arrayEnd, str_repeat(' ', $this->spacesAroundBrackets));
		} else {
			$phpcsFile->fixer->replaceToken($whitespacePointer, str_repeat(' ', $this->spacesAroundBrackets));
		}
	}

	private function checkWhitespaceBeforeComma(File $phpcsFile, int $comma): void
	{
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$comma - 1]['code'] !== T_WHITESPACE) {
			return;
		}

		if ($tokens[$comma - 2]['code'] === T_COMMA) {
			return;
		}

		$error = sprintf(
			'Expected 0 spaces between "%s" and comma, %d found.',
			$tokens[$comma - 2]['content'],
			$tokens[$comma - 1]['length'],
		);
		$fix = $phpcsFile->addFixableError($error, $comma, self::CODE_SPACE_BEFORE_COMMA);
		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->replaceToken($comma - 1, '');
	}

	private function checkWhitespaceAfterComma(File $phpcsFile, int $comma): void
	{
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$comma + 1]['code'] !== T_WHITESPACE) {
			$error = sprintf('Expected 1 space between comma and "%s", 0 found.', $tokens[$comma + 1]['content']);
			$fix = $phpcsFile->addFixableError($error, $comma, self::CODE_SPACE_AFTER_COMMA);
			if ($fix) {
				$phpcsFile->fixer->addContent($comma, ' ');
			}

			return;
		}

		$spaceLength = $tokens[$comma + 1]['length'];
		if ($spaceLength === 1) {
			return;
		}

		$error = sprintf('Expected 1 space between comma and "%s", %d found.', $tokens[$comma + 2]['content'], $spaceLength);
		$fix = $phpcsFile->addFixableError($error, $comma, self::CODE_SPACE_AFTER_COMMA);
		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->replaceToken($comma + 1, ' ');
	}

}
