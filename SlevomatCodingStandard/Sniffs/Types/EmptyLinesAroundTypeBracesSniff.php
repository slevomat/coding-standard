<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Types;

class EmptyLinesAroundTypeBracesSniff implements \PHP_CodeSniffer_Sniff
{

	const CODE_NO_EMPTY_LINE_AFTER_OPENING_BRACE = 'NoEmptyLineAfterOpeningBrace';

	const CODE_NO_EMPTY_LINE_BEFORE_CLOSING_BRACE = 'NoEmptyLineBeforeClosingBrace';

	/**
	 * @return int[]
	 */
	public function register(): array
	{
		return [
			T_CLASS,
			T_INTERFACE,
			T_TRAIT,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $stackPointer
	 */
	public function process(\PHP_CodeSniffer_File $phpcsFile, $stackPointer)
	{
		$tokens = $phpcsFile->getTokens();
		$typeToken = $tokens[$stackPointer];
		$openerPointer = $typeToken['scope_opener'];
		$openerToken = $tokens[$openerPointer];
		$nextPointerAfterOpeningBrace = $phpcsFile->findNext(T_WHITESPACE, $openerPointer + 1, null, true);
		$nextTokenAfterOpeningBrace = $tokens[$nextPointerAfterOpeningBrace];
		if ($nextTokenAfterOpeningBrace['line'] !== $openerToken['line'] + 2) {
			$phpcsFile->addError(sprintf(
				'There must be one empty line after %s opening brace.',
				$typeToken['content']
			), $nextPointerAfterOpeningBrace, self::CODE_NO_EMPTY_LINE_AFTER_OPENING_BRACE);
		}

		$closerPointer = $typeToken['scope_closer'];
		$closerToken = $tokens[$closerPointer];
		$previousPointerAfterClosingBrace = $phpcsFile->findPrevious(T_WHITESPACE, $closerPointer - 1, null, true);
		$previousTokenAfterClosingBrace = $tokens[$previousPointerAfterClosingBrace];
		if ($previousTokenAfterClosingBrace['line'] !== $closerToken['line'] - 2) {
			$phpcsFile->addError(sprintf(
				'There must be one empty line before %s closing brace.',
				$typeToken['content']
			), $previousPointerAfterClosingBrace, self::CODE_NO_EMPTY_LINE_BEFORE_CLOSING_BRACE);
		}
	}

}
