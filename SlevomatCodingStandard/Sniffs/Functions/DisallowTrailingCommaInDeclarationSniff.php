<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use const T_COMMA;
use const T_WHITESPACE;

class DisallowTrailingCommaInDeclarationSniff implements Sniff
{

	public const CODE_DISALLOWED_TRAILING_COMMA = 'DisallowedTrailingComma';

	public bool $onlySingleLine = false;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return TokenHelper::FUNCTION_TOKEN_CODES;
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $functionPointer
	 */
	public function process(File $phpcsFile, $functionPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$parenthesisOpenerPointer = $tokens[$functionPointer]['parenthesis_opener'];
		$parenthesisCloserPointer = $tokens[$functionPointer]['parenthesis_closer'];

		$pointerBeforeParenthesisCloser = TokenHelper::findPreviousExcluding(
			$phpcsFile,
			T_WHITESPACE,
			$parenthesisCloserPointer - 1,
			$parenthesisOpenerPointer,
		);

		if ($tokens[$pointerBeforeParenthesisCloser]['code'] !== T_COMMA) {
			return;
		}

		if ($this->onlySingleLine && $tokens[$parenthesisOpenerPointer]['line'] !== $tokens[$parenthesisCloserPointer]['line']) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'Trailing comma after the last parameter in function declaration is disallowed.',
			$pointerBeforeParenthesisCloser,
			self::CODE_DISALLOWED_TRAILING_COMMA,
		);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();
		$phpcsFile->fixer->replaceToken($pointerBeforeParenthesisCloser, '');

		if ($tokens[$pointerBeforeParenthesisCloser]['line'] === $tokens[$parenthesisCloserPointer]['line']) {
			FixerHelper::removeBetween($phpcsFile, $pointerBeforeParenthesisCloser, $parenthesisCloserPointer);
		}

		$phpcsFile->fixer->endChangeset();
	}

}
