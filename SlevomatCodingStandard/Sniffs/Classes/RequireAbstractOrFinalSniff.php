<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function in_array;
use const T_ABSTRACT;
use const T_CLASS;
use const T_FINAL;
use const T_READONLY;

class RequireAbstractOrFinalSniff implements Sniff
{

	public const CODE_NO_ABSTRACT_OR_FINAL = 'ClassNeitherAbstractNorFinal';

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_CLASS,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $classPointer
	 */
	public function process(File $phpcsFile, $classPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$previousPointer = TokenHelper::findPreviousEffective($phpcsFile, $classPointer - 1);

		if ($tokens[$previousPointer]['code'] === T_READONLY) {
			$previousPointer = TokenHelper::findPreviousEffective($phpcsFile, $previousPointer - 1);
		}

		if (in_array($tokens[$previousPointer]['code'], [T_ABSTRACT, T_FINAL], true)) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'All classes should be declared using either the "abstract" or "final" keyword.',
			$classPointer,
			self::CODE_NO_ABSTRACT_OR_FINAL
		);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();
		$phpcsFile->fixer->addContentBefore($classPointer, 'final ');
		$phpcsFile->fixer->endChangeset();
	}

}
