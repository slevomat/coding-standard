<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Arrays;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\ArrayHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function in_array;
use const T_COMMA;
use const T_END_HEREDOC;
use const T_END_NOWDOC;

class TrailingArrayCommaSniff implements Sniff
{

	public const CODE_MISSING_TRAILING_COMMA = 'MissingTrailingComma';

	public ?bool $enableAfterHeredoc = null;

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
	public function process(File $phpcsFile, $stackPointer): void
	{
		$this->enableAfterHeredoc = SniffSettingsHelper::isEnabledByPhpVersion($this->enableAfterHeredoc, 70300);

		$tokens = $phpcsFile->getTokens();

		[$arrayOpenerPointer, $arrayCloserPointer] = ArrayHelper::openClosePointers($tokens[$stackPointer]);

		if ($tokens[$arrayOpenerPointer]['line'] === $tokens[$arrayCloserPointer]['line']) {
			return;
		}

		/** @var int $pointerPreviousToClose */
		$pointerPreviousToClose = TokenHelper::findPreviousEffective($phpcsFile, $arrayCloserPointer - 1);
		$tokenPreviousToClose = $tokens[$pointerPreviousToClose];

		if (
			$pointerPreviousToClose === $arrayOpenerPointer
			|| $tokenPreviousToClose['code'] === T_COMMA
			|| $tokens[$arrayCloserPointer]['line'] === $tokenPreviousToClose['line']
		) {
			return;
		}

		if (
			!$this->enableAfterHeredoc
			&& in_array($tokenPreviousToClose['code'], [T_END_HEREDOC, T_END_NOWDOC], true)
		) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'Multi-line arrays must have a trailing comma after the last element.',
			$pointerPreviousToClose,
			self::CODE_MISSING_TRAILING_COMMA,
		);
		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();
		$phpcsFile->fixer->addContent($pointerPreviousToClose, ',');
		$phpcsFile->fixer->endChangeset();
	}

}
