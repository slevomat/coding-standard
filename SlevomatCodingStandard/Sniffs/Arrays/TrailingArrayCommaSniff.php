<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Arrays;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function in_array;
use const T_ARRAY;
use const T_COMMA;
use const T_END_HEREDOC;
use const T_END_NOWDOC;
use const T_OPEN_SHORT_ARRAY;

class TrailingArrayCommaSniff implements Sniff
{

	public const CODE_MISSING_TRAILING_COMMA = 'MissingTrailingComma';

	/** @var bool|null */
	public $enableAfterHeredoc = null;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_OPEN_SHORT_ARRAY,
			T_ARRAY,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $stackPointer
	 */
	public function process(File $phpcsFile, $stackPointer): void
	{
		$this->enableAfterHeredoc = SniffSettingsHelper::isEnabledByPhpVersion($this->enableAfterHeredoc, 70300);

		$tokens = $phpcsFile->getTokens();
		$token = $tokens[$stackPointer];
		$isShortArray = $token['code'] === T_OPEN_SHORT_ARRAY;
		$pointerOpener = $isShortArray
			? $token['bracket_opener']
			: $token['parenthesis_opener'];
		$pointerCloser = $isShortArray
			? $token['bracket_closer']
			: $token['parenthesis_closer'];
		$tokenOpener = $tokens[$pointerOpener];
		$tokenCloser = $tokens[$pointerCloser];

		if ($tokenOpener['line'] === $tokenCloser['line']) {
			return;
		}

		/** @var int $pointerPreviousToClose */
		$pointerPreviousToClose = TokenHelper::findPreviousEffective($phpcsFile, $pointerCloser - 1);
		$tokenPreviousToClose = $tokens[$pointerPreviousToClose];

		if (
			$pointerPreviousToClose === $pointerOpener
			|| $tokenPreviousToClose['code'] === T_COMMA
			|| $tokenCloser['line'] === $tokenPreviousToClose['line']
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
			self::CODE_MISSING_TRAILING_COMMA
		);
		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();
		$phpcsFile->fixer->addContent($pointerPreviousToClose, ',');
		$phpcsFile->fixer->endChangeset();
	}

}
