<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\IndentationHelper;
use SlevomatCodingStandard\Helpers\TernaryOperatorHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use const T_INLINE_ELSE;
use const T_INLINE_THEN;
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

	public function process(File $phpcsFile, int $inlineThenPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$nextPointer = TokenHelper::findNextEffective($phpcsFile, $inlineThenPointer + 1);
		if ($tokens[$nextPointer]['code'] === T_INLINE_ELSE) {
			return;
		}

		if ($tokens[$inlineThenPointer]['line'] === $tokens[$nextPointer]['line']) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'Ternary operator should be reformatted as leading the line.',
			$inlineThenPointer,
			self::CODE_TRAILING_MULTI_LINE_TERNARY_OPERATOR_USED,
		);

		if (!$fix) {
			return;
		}

		$inlineElsePointer = TernaryOperatorHelper::getElsePointer($phpcsFile, $inlineThenPointer);

		$pointerBeforeInlineThen = TokenHelper::findPreviousEffective($phpcsFile, $inlineThenPointer - 1);
		$pointerAfterInlineThen = TokenHelper::findNextExcluding($phpcsFile, [T_WHITESPACE], $inlineThenPointer + 1);
		$pointerBeforeInlineElse = TokenHelper::findPreviousEffective($phpcsFile, $inlineElsePointer - 1);
		$pointerAfterInlineElse = TokenHelper::findNextExcluding($phpcsFile, [T_WHITESPACE], $inlineElsePointer + 1);

		$indentation = IndentationHelper::addIndentation(
			$phpcsFile,
			IndentationHelper::getIndentation(
				$phpcsFile,
				TokenHelper::findFirstNonWhitespaceOnLine($phpcsFile, $inlineThenPointer),
			),
		);

		$phpcsFile->fixer->beginChangeset();

		FixerHelper::removeBetween($phpcsFile, $pointerBeforeInlineThen, $inlineThenPointer);
		FixerHelper::removeBetween($phpcsFile, $inlineThenPointer, $pointerAfterInlineThen);

		FixerHelper::addBefore($phpcsFile, $inlineThenPointer, $phpcsFile->eolChar . $indentation);
		FixerHelper::addBefore($phpcsFile, $pointerAfterInlineThen, ' ');

		FixerHelper::removeBetween($phpcsFile, $pointerBeforeInlineElse, $inlineElsePointer);
		FixerHelper::removeBetween($phpcsFile, $inlineElsePointer, $pointerAfterInlineElse);

		FixerHelper::addBefore($phpcsFile, $inlineElsePointer, $phpcsFile->eolChar . $indentation);
		FixerHelper::addBefore($phpcsFile, $pointerAfterInlineElse, ' ');

		$phpcsFile->fixer->endChangeset();
	}

}
