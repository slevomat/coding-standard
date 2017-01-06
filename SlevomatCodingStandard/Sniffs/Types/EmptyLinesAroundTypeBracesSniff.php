<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Types;

class EmptyLinesAroundTypeBracesSniff implements \PHP_CodeSniffer_Sniff
{

	const CODE_NO_EMPTY_LINE_AFTER_OPENING_BRACE = 'NoEmptyLineAfterOpeningBrace';

	const CODE_MULTIPLE_EMPTY_LINES_AFTER_OPENING_BRACE = 'MultipleEmptyLinesAfterOpeningBrace';

	const CODE_NO_EMPTY_LINE_BEFORE_CLOSING_BRACE = 'NoEmptyLineBeforeClosingBrace';

	const CODE_MULTIPLE_EMPTY_LINES_BEFORE_CLOSING_BRACE = 'MultipleEmptyLinesBeforeClosingBrace';

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

		$lines = $nextTokenAfterOpeningBrace['line'] - $openerToken['line'] - 1;
		if ($lines === 0) {
			$fix = $phpcsFile->addFixableError(sprintf(
				'There must be one empty line after %s opening brace.',
				$typeToken['content']
			), $openerPointer, self::CODE_NO_EMPTY_LINE_AFTER_OPENING_BRACE);

			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->addNewline($openerPointer);
				$phpcsFile->fixer->endChangeset();
			}
		} elseif ($lines > 1) {
			$fix = $phpcsFile->addFixableError(sprintf(
				'There must be one empty line after %s opening brace.',
				$typeToken['content']
			), $openerPointer, self::CODE_MULTIPLE_EMPTY_LINES_AFTER_OPENING_BRACE);

			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				for ($i = $openerPointer + 3; $i < $nextPointerAfterOpeningBrace; $i++) {
					if ($tokens[$i]['content'] !== $phpcsFile->eolChar) {
						break;
					}
					$phpcsFile->fixer->replaceToken($i, '');
				}
				$phpcsFile->fixer->endChangeset();
			}
		}

		$closerPointer = $typeToken['scope_closer'];
		$closerToken = $tokens[$closerPointer];
		$previousPointerBeforeClosingBrace = $phpcsFile->findPrevious(T_WHITESPACE, $closerPointer - 1, null, true);
		$previousTokenBeforeClosingBrace = $tokens[$previousPointerBeforeClosingBrace];

		$lines = $closerToken['line'] - $previousTokenBeforeClosingBrace['line'] - 1;
		if ($lines === 0) {
			$fix = $phpcsFile->addFixableError(sprintf(
				'There must be one empty line before %s closing brace.',
				$typeToken['content']
			), $closerPointer, self::CODE_NO_EMPTY_LINE_BEFORE_CLOSING_BRACE);

			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->addNewlineBefore($closerPointer);
				$phpcsFile->fixer->endChangeset();
			}
		} elseif ($lines > 1) {
			$fix = $phpcsFile->addFixableError(sprintf(
				'There must be one empty line before %s closing brace.',
				$typeToken['content']
			), $closerPointer, self::CODE_MULTIPLE_EMPTY_LINES_BEFORE_CLOSING_BRACE);

			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				for ($i = $previousPointerBeforeClosingBrace + 3; $i < $closerPointer; $i++) {
					$phpcsFile->fixer->replaceToken($i, '');
				}
				$phpcsFile->fixer->endChangeset();
			}
		}
	}

}
