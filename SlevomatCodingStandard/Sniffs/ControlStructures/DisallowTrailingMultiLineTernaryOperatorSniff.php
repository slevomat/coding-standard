<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\IndentationHelper;
use SlevomatCodingStandard\Helpers\TernaryOperatorHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function array_merge;
use function in_array;
use function strlen;
use function substr;
use function trim;
use const T_INLINE_ELSE;
use const T_INLINE_THEN;
use const T_OPEN_TAG;
use const T_OPEN_TAG_WITH_ECHO;
use const T_WHITESPACE;

class DisallowTrailingMultiLineTernaryOperatorSniff implements Sniff
{

	public const CODE_TRAILING_MULTI_LINE_TERNARY_OPERATOR_USED = 'TrailingMultiLineTernaryOperatorUsed';

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_INLINE_THEN,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $inlineThenPointer
	 */
	public function process(File $phpcsFile, $inlineThenPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$nextPointer = TokenHelper::findNextEffective($phpcsFile, $inlineThenPointer + 1);
		if ($tokens[$nextPointer]['code'] === T_INLINE_ELSE) {
			return;
		}

		$inlineElsePointer = TernaryOperatorHelper::getElsePointer($phpcsFile, $inlineThenPointer);

		if ($tokens[$inlineThenPointer]['line'] === $tokens[$inlineElsePointer]['line']) {
			return;
		}

		$endOfLineBeforeInlineThenPointer = $this->getEndOfLineBefore($phpcsFile, $inlineThenPointer);
		$endOfLineBeforeInlineElsePointer = $this->getEndOfLineBefore($phpcsFile, $inlineElsePointer);

		$contentBeforeThen = TokenHelper::getContent($phpcsFile, $endOfLineBeforeInlineThenPointer + 1, $inlineThenPointer - 1);
		$contentBeforeElse = TokenHelper::getContent($phpcsFile, $endOfLineBeforeInlineElsePointer + 1, $inlineElsePointer - 1);
		if (trim($contentBeforeElse) === '' && trim($contentBeforeThen) === '') {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'Ternary operator should be reformatted as leading the line.',
			$inlineThenPointer,
			self::CODE_TRAILING_MULTI_LINE_TERNARY_OPERATOR_USED
		);

		if (!$fix) {
			return;
		}

		$indentation = $this->getIndentation($phpcsFile, $endOfLineBeforeInlineThenPointer);
		$pointerBeforeInlineThen = TokenHelper::findPreviousEffective($phpcsFile, $inlineThenPointer - 1);
		$pointerAfterInlineThen = TokenHelper::findNextExcluding($phpcsFile, [T_WHITESPACE], $inlineThenPointer + 1);
		$pointerBeforeInlineElse = TokenHelper::findPreviousEffective($phpcsFile, $inlineElsePointer - 1);
		$pointerAfterInlineElse = TokenHelper::findNextExcluding($phpcsFile, [T_WHITESPACE], $inlineElsePointer + 1);

		$phpcsFile->fixer->beginChangeset();

		FixerHelper::removeBetween($phpcsFile, $pointerBeforeInlineThen, $inlineThenPointer);
		FixerHelper::removeBetween($phpcsFile, $inlineThenPointer, $pointerAfterInlineThen);

		$phpcsFile->fixer->addContentBefore($inlineThenPointer, $phpcsFile->eolChar . $indentation);
		$phpcsFile->fixer->addContentBefore($pointerAfterInlineThen, ' ');

		FixerHelper::removeBetween($phpcsFile, $pointerBeforeInlineElse, $inlineElsePointer);
		FixerHelper::removeBetween($phpcsFile, $inlineElsePointer, $pointerAfterInlineElse);

		$phpcsFile->fixer->addContentBefore($inlineElsePointer, $phpcsFile->eolChar . $indentation);
		$phpcsFile->fixer->addContentBefore($pointerAfterInlineElse, ' ');

		$phpcsFile->fixer->endChangeset();
	}

	private function getEndOfLineBefore(File $phpcsFile, int $pointer): int
	{
		$tokens = $phpcsFile->getTokens();

		$endOfLineBefore = null;

		$startPointer = $pointer - 1;
		while (true) {
			$possibleEndOfLinePointer = TokenHelper::findPrevious(
				$phpcsFile,
				array_merge([T_WHITESPACE, T_OPEN_TAG, T_OPEN_TAG_WITH_ECHO], TokenHelper::$inlineCommentTokenCodes),
				$startPointer
			);
			if (
				$tokens[$possibleEndOfLinePointer]['code'] === T_WHITESPACE
				&& $tokens[$possibleEndOfLinePointer]['content'] === $phpcsFile->eolChar
			) {
				$endOfLineBefore = $possibleEndOfLinePointer;
				break;
			}

			if (
				in_array($tokens[$possibleEndOfLinePointer]['code'], TokenHelper::$inlineCommentTokenCodes, true)
				&& substr($tokens[$possibleEndOfLinePointer]['content'], -1) === $phpcsFile->eolChar
			) {
				$endOfLineBefore = $possibleEndOfLinePointer;
				break;
			}

			$startPointer = $possibleEndOfLinePointer - 1;
		}

		/** @var int $endOfLineBefore */
		$endOfLineBefore = $endOfLineBefore;
		return $endOfLineBefore;
	}

	private function getIndentation(File $phpcsFile, int $endOfLinePointer): string
	{
		$pointerAfterWhitespace = TokenHelper::findNextNonWhitespace($phpcsFile, $endOfLinePointer + 1);
		$actualIndentation = TokenHelper::getContent($phpcsFile, $endOfLinePointer + 1, $pointerAfterWhitespace - 1);

		if (strlen($actualIndentation) !== 0) {
			return $actualIndentation . (
				substr($actualIndentation, -1) === IndentationHelper::TAB_INDENT
					? IndentationHelper::TAB_INDENT
					: IndentationHelper::SPACES_INDENT
			);
		}

		$tabPointer = TokenHelper::findPreviousContent($phpcsFile, T_WHITESPACE, IndentationHelper::TAB_INDENT, $endOfLinePointer - 1);
		return $tabPointer !== null ? IndentationHelper::TAB_INDENT : IndentationHelper::SPACES_INDENT;
	}

}
