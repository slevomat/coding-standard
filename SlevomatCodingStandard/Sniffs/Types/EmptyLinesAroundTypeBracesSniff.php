<?php

namespace SlevomatCodingStandard\Sniffs\Types;

use PHP_CodeSniffer_File;

class EmptyLinesAroundTypeBracesSniff implements \PHP_CodeSniffer_Sniff
{

	const CODE_EMPTY_LINE_BEFORE_CLOSING_BRACE = 'EmptyLineBeforeClosingBrace';

	const CODE_EMPTY_LINE_AFTER_OPENING_BRACE = 'EmptyLineAfterOpeningBrace';

	/**
	 * @return integer[]
	 */
	public function register()
	{
		return [
			T_CLASS,
			T_INTERFACE,
			T_TRAIT,
		];
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param integer $stackPointer
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPointer)
	{
		$tokens = $phpcsFile->getTokens();
		$typeToken = $tokens[$stackPointer];
		$openerPointer = $typeToken['scope_opener'];
		$openerToken = $tokens[$openerPointer];
		$nextPointerAfterOpeningBrace = $phpcsFile->findNext(T_WHITESPACE, $openerPointer + 1, null, true);
		$nextTokenAfterOpeningBrace = $tokens[$nextPointerAfterOpeningBrace];
		if ($nextTokenAfterOpeningBrace['line'] !== $openerToken['line'] + 2) {
			$fix = $phpcsFile->addFixableError(sprintf(
				'There must be one empty line after %s opening brace.',
				$typeToken['content']
			), $nextPointerAfterOpeningBrace, self::CODE_EMPTY_LINE_AFTER_OPENING_BRACE);

			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->addNewline($nextPointerAfterOpeningBrace);
				$phpcsFile->fixer->endChangeset();
			}
		}

		$closerPointer = $typeToken['scope_closer'];
		$closerToken = $tokens[$closerPointer];
		$previousPointerAfterClosingBrace = $phpcsFile->findPrevious(T_WHITESPACE, $closerPointer - 1, null, true);
		$previousTokenAfterClosingBrace = $tokens[$previousPointerAfterClosingBrace];
		if ($previousTokenAfterClosingBrace['line'] !== $closerToken['line'] - 2) {
			$fix = $phpcsFile->addFixableError(sprintf(
				'There must be one empty line before %s closing brace.',
				$typeToken['content']
			), $previousPointerAfterClosingBrace, self::CODE_EMPTY_LINE_BEFORE_CLOSING_BRACE);

			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->addNewline($previousPointerAfterClosingBrace);
				$phpcsFile->fixer->endChangeset();
			}
		}
	}

}
