<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function in_array;
use const T_CLOSE_PARENTHESIS;
use const T_COMMA;
use const T_ISSET;
use const T_OPEN_PARENTHESIS;
use const T_STRING;
use const T_UNSET;
use const T_VARIABLE;

class RequireTrailingCommaInCallSniff implements Sniff
{

	public const CODE_MISSING_TRAILING_COMMA = 'MissingTrailingComma';

	public ?bool $enable = null;

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
		$this->enable = SniffSettingsHelper::isEnabledByPhpVersion($this->enable, 70300);

		if (!$this->enable) {
			return;
		}

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

		if ($tokens[$parenthesisOpenerPointer]['line'] === $tokens[$parenthesisCloserPointer]['line']) {
			return;
		}

		$pointerBeforeParenthesisCloser = TokenHelper::findPreviousEffective($phpcsFile, $parenthesisCloserPointer - 1);
		if ($pointerBeforeParenthesisCloser === $parenthesisOpenerPointer) {
			return;
		}

		if ($tokens[$parenthesisCloserPointer]['line'] === $tokens[$pointerBeforeParenthesisCloser]['line']) {
			return;
		}

		if ($tokens[$pointerBeforeParenthesisCloser]['code'] === T_COMMA) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'Multi-line function calls must have a trailing comma after the last parameter.',
			$pointerBeforeParenthesisCloser,
			self::CODE_MISSING_TRAILING_COMMA,
		);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();
		FixerHelper::add($phpcsFile, $pointerBeforeParenthesisCloser, ',');
		$phpcsFile->fixer->endChangeset();
	}

}
