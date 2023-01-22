<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\IndentationHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TernaryOperatorHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function array_merge;
use function in_array;
use function strlen;
use function substr;
use const T_INLINE_ELSE;
use const T_INLINE_THEN;
use const T_OPEN_TAG;
use const T_OPEN_TAG_WITH_ECHO;
use const T_SEMICOLON;
use const T_WHITESPACE;

class RequireMultiLineTernaryOperatorSniff implements Sniff
{

	public const CODE_MULTI_LINE_TERNARY_OPERATOR_NOT_USED = 'MultiLineTernaryOperatorNotUsed';

	/** @var int */
	public $lineLengthLimit = 0;

	/** @var int|null */
	public $minExpressionsLength = null;

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
		$this->lineLengthLimit = SniffSettingsHelper::normalizeInteger($this->lineLengthLimit);
		$this->minExpressionsLength = SniffSettingsHelper::normalizeNullableInteger($this->minExpressionsLength);

		$tokens = $phpcsFile->getTokens();

		$nextPointer = TokenHelper::findNextEffective($phpcsFile, $inlineThenPointer + 1);
		if ($tokens[$nextPointer]['code'] === T_INLINE_ELSE) {
			return;
		}

		$inlineElsePointer = TernaryOperatorHelper::getElsePointer($phpcsFile, $inlineThenPointer);

		if ($tokens[$inlineThenPointer]['line'] !== $tokens[$inlineElsePointer]['line']) {
			return;
		}

		$inlineElseEndPointer = TernaryOperatorHelper::getEndPointer($phpcsFile, $inlineThenPointer, $inlineElsePointer);
		$pointerAfterInlineElseEnd = TokenHelper::findNextEffective($phpcsFile, $inlineElseEndPointer + 1);

		if ($pointerAfterInlineElseEnd === null || $tokens[$pointerAfterInlineElseEnd]['code'] !== T_SEMICOLON) {
			return;
		}

		$endOfLineBeforeInlineThenPointer = $this->getEndOfLineBefore($phpcsFile, $inlineThenPointer);

		$actualLineLength = strlen(TokenHelper::getContent($phpcsFile, $endOfLineBeforeInlineThenPointer + 1, $pointerAfterInlineElseEnd));

		if ($actualLineLength <= $this->lineLengthLimit) {
			return;
		}

		$expressionsLength = strlen(TokenHelper::getContent($phpcsFile, $inlineThenPointer + 1, $pointerAfterInlineElseEnd - 1));

		if (
			$this->minExpressionsLength !== null
			&& $this->minExpressionsLength >= $expressionsLength
		) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'Ternary operator should be reformatted to more lines.',
			$inlineThenPointer,
			self::CODE_MULTI_LINE_TERNARY_OPERATOR_NOT_USED
		);

		if (!$fix) {
			return;
		}

		$indentation = $this->getIndentation($phpcsFile, $endOfLineBeforeInlineThenPointer);
		$pointerBeforeInlineThen = TokenHelper::findPreviousEffective($phpcsFile, $inlineThenPointer - 1);
		$pointerBeforeInlineElse = TokenHelper::findPreviousEffective($phpcsFile, $inlineElsePointer - 1);

		$phpcsFile->fixer->beginChangeset();

		FixerHelper::removeBetween($phpcsFile, $pointerBeforeInlineThen, $inlineThenPointer);

		$phpcsFile->fixer->addContentBefore($inlineThenPointer, $phpcsFile->eolChar . $indentation);

		FixerHelper::removeBetween($phpcsFile, $pointerBeforeInlineElse, $inlineElsePointer);

		$phpcsFile->fixer->addContentBefore($inlineElsePointer, $phpcsFile->eolChar . $indentation);

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
				$tokens[$possibleEndOfLinePointer]['code'] === T_OPEN_TAG
				|| $tokens[$possibleEndOfLinePointer]['code'] === T_OPEN_TAG_WITH_ECHO
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
			return $actualIndentation . (substr(
				$actualIndentation,
				-1
			) === IndentationHelper::TAB_INDENT ? IndentationHelper::TAB_INDENT : IndentationHelper::SPACES_INDENT);
		}

		$tabPointer = TokenHelper::findPreviousContent($phpcsFile, T_WHITESPACE, IndentationHelper::TAB_INDENT, $endOfLinePointer - 1);
		return $tabPointer !== null ? IndentationHelper::TAB_INDENT : IndentationHelper::SPACES_INDENT;
	}

}
