<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use const T_CLOSURE;
use const T_COMMA;
use const T_USE;
use const T_WHITESPACE;

class DisallowTrailingCommaInClosureUseSniff implements Sniff
{

	public const CODE_DISALLOWED_TRAILING_COMMA = 'DisallowedTrailingComma';

	public bool $onlySingleLine = false;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_CLOSURE,
		];
	}

	public function process(File $phpcsFile, int $functionPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$parenthesisCloserPointer = $tokens[$functionPointer]['parenthesis_closer'];

		$usePointer = TokenHelper::findNextEffective($phpcsFile, $parenthesisCloserPointer + 1);

		if ($tokens[$usePointer]['code'] !== T_USE) {
			return;
		}

		$useParenthesisOpenerPointer = TokenHelper::findNextEffective($phpcsFile, $usePointer + 1);
		$useParenthesisCloserPointer = $tokens[$useParenthesisOpenerPointer]['parenthesis_closer'];

		$pointerBeforeUseParenthesisCloser = TokenHelper::findPreviousExcluding(
			$phpcsFile,
			T_WHITESPACE,
			$tokens[$useParenthesisOpenerPointer]['parenthesis_closer'] - 1,
			$useParenthesisOpenerPointer,
		);

		if ($tokens[$pointerBeforeUseParenthesisCloser]['code'] !== T_COMMA) {
			return;
		}

		if ($this->onlySingleLine && $tokens[$useParenthesisOpenerPointer]['line'] !== $tokens[$useParenthesisCloserPointer]['line']) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'Trailing comma after the last inherited variable in "use" of closure declaration is disallowed.',
			$pointerBeforeUseParenthesisCloser,
			self::CODE_DISALLOWED_TRAILING_COMMA,
		);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();
		FixerHelper::replace($phpcsFile, $pointerBeforeUseParenthesisCloser, '');

		if ($tokens[$pointerBeforeUseParenthesisCloser]['line'] === $tokens[$useParenthesisCloserPointer]['line']) {
			FixerHelper::removeBetween($phpcsFile, $pointerBeforeUseParenthesisCloser, $useParenthesisCloserPointer);
		}

		$phpcsFile->fixer->endChangeset();
	}

}
