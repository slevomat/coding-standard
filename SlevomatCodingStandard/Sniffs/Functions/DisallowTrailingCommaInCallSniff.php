<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function in_array;
use const T_CLOSE_PARENTHESIS;
use const T_COMMA;
use const T_ISSET;
use const T_OPEN_PARENTHESIS;
use const T_STRING;
use const T_UNSET;
use const T_VARIABLE;

class DisallowTrailingCommaInCallSniff implements Sniff
{

	public const CODE_DISALLOWED_TRAILING_COMMA = 'DisallowedTrailingComma';

	public bool $onlySingleLine = false;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_OPEN_PARENTHESIS,
		];
	}

	public function process(File $phpcsFile, int $parenthesisOpenerPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$pointerBeforeParenthesisOpener = TokenHelper::findPreviousEffective($phpcsFile, $parenthesisOpenerPointer - 1);
		if (!in_array(
			$tokens[$pointerBeforeParenthesisOpener]['code'],
			[...TokenHelper::NAME_TOKEN_CODES, T_STRING, T_VARIABLE, T_ISSET, T_UNSET, T_CLOSE_PARENTHESIS, ...TokenHelper::CLASS_KEYWORD_CODES],
			true,
		)) {
			return;
		}

		$functionPointer = TokenHelper::findPreviousEffective($phpcsFile, $pointerBeforeParenthesisOpener - 1);
		if (in_array($tokens[$functionPointer]['code'], TokenHelper::FUNCTION_TOKEN_CODES, true)) {
			return;
		}

		$parenthesisCloserPointer = $tokens[$parenthesisOpenerPointer]['parenthesis_closer'];
		$pointerBeforeParenthesisCloser = TokenHelper::findPreviousEffective($phpcsFile, $parenthesisCloserPointer - 1);

		if ($tokens[$pointerBeforeParenthesisCloser]['code'] !== T_COMMA) {
			return;
		}

		if ($this->onlySingleLine && $tokens[$parenthesisOpenerPointer]['line'] !== $tokens[$parenthesisCloserPointer]['line']) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'Trailing comma after the last parameter in function call is disallowed.',
			$pointerBeforeParenthesisCloser,
			self::CODE_DISALLOWED_TRAILING_COMMA,
		);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();
		FixerHelper::replace($phpcsFile, $pointerBeforeParenthesisCloser, '');

		if ($tokens[$pointerBeforeParenthesisCloser]['line'] === $tokens[$parenthesisCloserPointer]['line']) {
			FixerHelper::removeBetween($phpcsFile, $pointerBeforeParenthesisCloser, $parenthesisCloserPointer);
		}

		$phpcsFile->fixer->endChangeset();
	}

}
