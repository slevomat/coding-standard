<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\TokenHelper;
use const T_CLOSURE;
use const T_COMMA;
use const T_USE;
use const T_WHITESPACE;

class DisallowTrailingCommaInClosureUseSniff implements Sniff
{

	public const CODE_DISALLOWED_TRAILING_COMMA = 'DisallowedTrailingComma';

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_CLOSURE,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $functionPointer
	 */
	public function process(File $phpcsFile, $functionPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$parenthesisCloserPointer = $tokens[$functionPointer]['parenthesis_closer'];

		$usePointer = TokenHelper::findNextEffective($phpcsFile, $parenthesisCloserPointer + 1);

		if ($tokens[$usePointer]['code'] !== T_USE) {
			return;
		}

		$useParenthesisOpener = TokenHelper::findNextEffective($phpcsFile, $usePointer + 1);

		$pointerBeforeUseParenthesisCloser = TokenHelper::findPreviousExcluding(
			$phpcsFile,
			T_WHITESPACE,
			$tokens[$useParenthesisOpener]['parenthesis_closer'] - 1,
			$useParenthesisOpener
		);

		if ($tokens[$pointerBeforeUseParenthesisCloser]['code'] !== T_COMMA) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'Trailing comma after the last inherited variable in "use" of closure declaration is disallowed.',
			$pointerBeforeUseParenthesisCloser,
			self::CODE_DISALLOWED_TRAILING_COMMA
		);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();
		$phpcsFile->fixer->replaceToken($pointerBeforeUseParenthesisCloser, '');
		$phpcsFile->fixer->endChangeset();
	}

}
