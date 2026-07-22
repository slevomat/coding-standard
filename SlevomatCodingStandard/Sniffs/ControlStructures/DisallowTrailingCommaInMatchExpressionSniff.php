<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use const T_COMMA;
use const T_MATCH;

class DisallowTrailingCommaInMatchExpressionSniff implements Sniff
{

	public const CODE_DISALLOWED_TRAILING_COMMA = 'DisallowedTrailingComma';

	public bool $onlySingleLine = false;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_MATCH,
		];
	}

	public function process(File $phpcsFile, int $stackPtr): void
	{
		$tokens = $phpcsFile->getTokens();

		$scopeCloser = $tokens[$stackPtr]['scope_closer'];
		$pointerBeforeScopeCloser = TokenHelper::findPreviousEffective($phpcsFile, $scopeCloser - 1);

		if ($tokens[$pointerBeforeScopeCloser]['code'] !== T_COMMA) {
			return;
		}

		if ($this->onlySingleLine && $tokens[$pointerBeforeScopeCloser]['line'] !== $tokens[$scopeCloser]['line']) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'Trailing comma after the last branch of a match expression is disallowed.',
			$pointerBeforeScopeCloser,
			self::CODE_DISALLOWED_TRAILING_COMMA,
		);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();
		FixerHelper::replace($phpcsFile, $pointerBeforeScopeCloser, '');

		if ($tokens[$pointerBeforeScopeCloser]['line'] === $tokens[$scopeCloser]['line']) {
			FixerHelper::removeBetween($phpcsFile, $pointerBeforeScopeCloser, $scopeCloser);
		}

		$phpcsFile->fixer->endChangeset();
	}

}
