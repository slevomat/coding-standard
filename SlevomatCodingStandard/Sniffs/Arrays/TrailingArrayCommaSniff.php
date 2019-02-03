<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Arrays;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\TokenHelper;
use const T_COMMA;
use const T_OPEN_SHORT_ARRAY;

class TrailingArrayCommaSniff implements Sniff
{

	public const CODE_MISSING_TRAILING_COMMA = 'MissingTrailingComma';

	/**
	 * @return (int|string)[]
	 */
	public function register(): array
	{
		return [
			T_OPEN_SHORT_ARRAY,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $stackPointer
	 */
	public function process(File $phpcsFile, $stackPointer): void
	{
		$tokens = $phpcsFile->getTokens();
		$arrayToken = $tokens[$stackPointer];
		$closeParenthesisPointer = $arrayToken['bracket_closer'];
		$openParenthesisToken = $tokens[$arrayToken['bracket_opener']];
		$closeParenthesisToken = $tokens[$closeParenthesisPointer];
		if ($openParenthesisToken['line'] === $closeParenthesisToken['line']) {
			return;
		}

		/** @var int $previousToCloseParenthesisPointer */
		$previousToCloseParenthesisPointer = TokenHelper::findPreviousEffective($phpcsFile, $closeParenthesisPointer - 1);
		$previousToCloseParenthesisToken = $tokens[$previousToCloseParenthesisPointer];
		if (
			$previousToCloseParenthesisPointer === $arrayToken['bracket_opener']
			|| $previousToCloseParenthesisToken['code'] === T_COMMA
			|| $closeParenthesisToken['line'] === $previousToCloseParenthesisToken['line']
		) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'Multi-line arrays must have a trailing comma after the last element.',
			$previousToCloseParenthesisPointer,
			self::CODE_MISSING_TRAILING_COMMA
		);
		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();
		$phpcsFile->fixer->addContent($previousToCloseParenthesisPointer, ',');
		$phpcsFile->fixer->endChangeset();
	}

}
