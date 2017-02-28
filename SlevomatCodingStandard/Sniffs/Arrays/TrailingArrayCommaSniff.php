<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Arrays;

use PHP_CodeSniffer_File;
use SlevomatCodingStandard\Helpers\TokenHelper;

class TrailingArrayCommaSniff implements \PHP_CodeSniffer_Sniff
{

	const CODE_MISSING_TRAILING_COMMA = 'missingTrailingComma';

	/**
	 * @return integer[]
	 */
	public function register()
	{
		return [
			T_OPEN_SHORT_ARRAY,
		];
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param integer $stackPointer
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPointer)
	{
		$tokens = $phpcsFile->getTokens();
		$arrayToken = $tokens[$stackPointer];
		$closeParenthesisPointer = $arrayToken['bracket_closer'];
		$openParenthesisToken = $tokens[$arrayToken['bracket_opener']];
		$closeParenthesisToken = $tokens[$closeParenthesisPointer];
		if ($openParenthesisToken['line'] === $closeParenthesisToken['line']) {
			return;
		}

		$previousToCloseParenthesisPointer = TokenHelper::findPreviousEffective($phpcsFile, $closeParenthesisPointer - 1);
		$previousToCloseParenthesisToken = $tokens[$previousToCloseParenthesisPointer];
		if ($previousToCloseParenthesisToken['code'] !== T_COMMA && $closeParenthesisToken['line'] !== $previousToCloseParenthesisToken['line']) {
			$fix = $phpcsFile->addFixableError(
				'Multiline arrays must have a trailing comma after the last element',
				$previousToCloseParenthesisPointer,
				self::CODE_MISSING_TRAILING_COMMA
			);
			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->addContent($previousToCloseParenthesisPointer, ',');
				$phpcsFile->fixer->endChangeset();
			}
		}
	}

}
