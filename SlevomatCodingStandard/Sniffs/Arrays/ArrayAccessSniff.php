<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Arrays;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\TokenHelper;
use const T_CLOSE_SQUARE_BRACKET;
use const T_OPEN_SQUARE_BRACKET;
use const T_VARIABLE;

class ArrayAccessSniff implements Sniff
{

	public const CODE_NO_SPACE_BEFORE_BRACKETS = 'NoSpaceBeforeBrackets';
	public const CODE_NO_SPACE_BETWEEN_BRACKETS = 'NoSpaceBetweenBrackets';

	/**
	 * @return list<string>
	 */
	public function register(): array
	{
		return [T_OPEN_SQUARE_BRACKET];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $stackPointer
	 */
	public function process(File $phpcsFile, $stackPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$previousToken = TokenHelper::findPreviousNonWhitespace($phpcsFile, $stackPointer - 1);

		if (
			$previousToken === null
			|| $previousToken === $stackPointer - 1) {
			return;
		}

		if ($tokens[$previousToken]['code'] === T_VARIABLE) {
			$this->addError(
				$phpcsFile,
				$stackPointer,
				'There should be no space between array variable and array access operator.',
				self::CODE_NO_SPACE_BEFORE_BRACKETS,
			);
		}

		if ($tokens[$previousToken]['code'] !== T_CLOSE_SQUARE_BRACKET) {
			return;
		}

		$this->addError(
			$phpcsFile,
			$stackPointer,
			'There should be no space between array access operators.',
			self::CODE_NO_SPACE_BETWEEN_BRACKETS,
		);
	}

	private function addError(File $phpcsFile, int $stackPointer, string $error, string $code): void
	{
		$fix = $phpcsFile->addFixableError($error, $stackPointer, $code);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();
		$phpcsFile->fixer->replaceToken($stackPointer - 1, '');
		$phpcsFile->fixer->endChangeset();
	}

}
