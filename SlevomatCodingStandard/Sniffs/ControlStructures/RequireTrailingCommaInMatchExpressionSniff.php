<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use const T_COMMA;
use const T_MATCH;

class RequireTrailingCommaInMatchExpressionSniff implements Sniff
{

	public const CODE_MISSING_TRAILING_COMMA = 'MissingTrailingComma';

	public ?bool $enable = null;

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
		$this->enable = SniffSettingsHelper::isEnabledByPhpVersion($this->enable, 80800);
		if (!$this->enable) {
			return;
		}

		$tokens = $phpcsFile->getTokens();
		$scopeCloser = $tokens[$stackPtr]['scope_closer'];
		$pointerBeforeScopeCloser = TokenHelper::findPreviousEffective($phpcsFile, $scopeCloser - 1);

		if ($tokens[$scopeCloser]['line'] === $tokens[$pointerBeforeScopeCloser]['line']) {
			return;
		}

		if ($tokens[$pointerBeforeScopeCloser]['code'] === T_COMMA) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'Multi-line match expressions must have a trailing comma after the last branch.',
			$pointerBeforeScopeCloser,
			self::CODE_MISSING_TRAILING_COMMA,
		);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();
		FixerHelper::add($phpcsFile, $pointerBeforeScopeCloser, ',');
		$phpcsFile->fixer->endChangeset();
	}

}
