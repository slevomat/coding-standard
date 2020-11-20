<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Exceptions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\ParameterHelper;
use SlevomatCodingStandard\Helpers\ScopeHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use const T_CATCH;
use const T_VARIABLE;

class RequireNonCapturingCatchSniff implements Sniff
{

	public const CODE_NON_CAPTURING_CATCH_REQUIRED = 'NonCapturingCatchRequired';

	/** @var bool|null */
	public $enable = null;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_CATCH,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param File $phpcsFile
	 * @param int $catchPointer
	 */
	public function process(File $phpcsFile, $catchPointer): void
	{
		$this->enable = SniffSettingsHelper::isEnabledByPhpVersion($this->enable, 80000);

		if (!$this->enable) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		$variablePointer = TokenHelper::findNext(
			$phpcsFile,
			T_VARIABLE,
			$tokens[$catchPointer]['parenthesis_opener'],
			$tokens[$catchPointer]['parenthesis_closer']
		);
		if ($variablePointer === null) {
			return;
		}

		for ($i = $tokens[$catchPointer]['scope_opener'] + 1; $i < $tokens[$catchPointer]['scope_closer']; $i++) {
			if ($tokens[$i]['code'] !== T_VARIABLE) {
				continue;
			}

			if (ParameterHelper::isParameter($phpcsFile, $i)) {
				continue;
			}

			if (!ScopeHelper::isInSameScope($phpcsFile, $tokens[$catchPointer]['scope_opener'] + 1, $i)) {
				continue;
			}

			if ($tokens[$i]['content'] === $tokens[$variablePointer]['content']) {
				return;
			}
		}

		$fix = $phpcsFile->addFixableError('Non capturing catch is required', $catchPointer, self::CODE_NON_CAPTURING_CATCH_REQUIRED);

		if (!$fix) {
			return;
		}

		$pointerBeforeVariable = TokenHelper::findPreviousEffective($phpcsFile, $variablePointer - 1);

		$phpcsFile->fixer->beginChangeset();

		for ($i = $pointerBeforeVariable + 1; $i < $tokens[$catchPointer]['parenthesis_closer']; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}

		$phpcsFile->fixer->endChangeset();
	}

}
