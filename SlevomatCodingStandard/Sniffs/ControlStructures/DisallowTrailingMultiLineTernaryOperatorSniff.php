<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\IndentationHelper;
use SlevomatCodingStandard\Helpers\TernaryOperatorHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use const T_INLINE_ELSE;
use const T_INLINE_THEN;
use const T_OPEN_TAG;
use const T_WHITESPACE;

/**
 * Disallows trailing ternary operators ("?" and ":") at the end of lines in multiline ternary expressions
 * and enforces that leading ternary operators are indented exactly one level deeper than the line where
 * the ternary condition begins, followed by a single space.
 *
 * Short ternary operators ("?:") are ignored. Also suppresses conflicting indentation checks from
 * Generic.WhiteSpace.ScopeIndent and Squiz.Functions.MultiLineFunctionDeclaration on ternary continuation lines.
 */
class DisallowTrailingMultiLineTernaryOperatorSniff implements Sniff
{

	/**
	 * Error code reported when a multiline ternary operator ("?" or ":") is trailing at the end of a line
	 * or has invalid leading indentation or spacing.
	 */
	public const CODE_TRAILING_MULTI_LINE_TERNARY_OPERATOR_USED = 'TrailingMultiLineTernaryOperatorUsed';

	/**
	 * Alias for CODE_TRAILING_MULTI_LINE_TERNARY_OPERATOR_USED for multiline ternary indentation checks.
	 */
	public const CODE_INVALID_MULTI_LINE_TERNARY_OPERATOR_INDENTATION = self::CODE_TRAILING_MULTI_LINE_TERNARY_OPERATOR_USED;

	/**
	 * Registers T_OPEN_TAG (prioritized ahead of Generic.WhiteSpace.ScopeIndent) and T_INLINE_THEN.
	 *
	 * @return array<int, (int|string)> List of token codes handled by this sniff.
	 */
	public function register(): array
	{
		IndentationHelper::prioritizeOpenTagListener($this);

		return [
			T_OPEN_TAG,
			T_INLINE_THEN,
		];
	}

	/**
	 * Processes T_OPEN_TAG to register conflict exclusions and processes T_INLINE_THEN ("?") along with its
	 * matching T_INLINE_ELSE (":") to enforce leading placement and exact indentation in multiline ternary expressions.
	 *
	 * Short ternary expressions ("?:") are skipped.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param int $inlineThenPointer The position of the current token in the token stack.
	 */
	public function process(File $phpcsFile, int $inlineThenPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$inlineThenPointer]['code'] === T_OPEN_TAG) {
			foreach (TokenHelper::findNextAll($phpcsFile, T_INLINE_THEN, $inlineThenPointer + 1) as $ternaryPointer) {
				TernaryOperatorHelper::ignoreConflictingIndentationSniffs($phpcsFile, $ternaryPointer);
			}

			return;
		}

		$nextPointer = TokenHelper::findNextEffective($phpcsFile, $inlineThenPointer + 1);
		if ($tokens[$nextPointer]['code'] === T_INLINE_ELSE) {
			return;
		}

		$inlineElsePointer = TernaryOperatorHelper::getElsePointer($phpcsFile, $inlineThenPointer);

		$pointerBeforeInlineThen = TokenHelper::findPreviousExcluding($phpcsFile, [T_WHITESPACE], $inlineThenPointer - 1);
		$pointerAfterInlineThen = TokenHelper::findNextExcluding($phpcsFile, [T_WHITESPACE], $inlineThenPointer + 1);
		$pointerBeforeInlineElse = TokenHelper::findPreviousExcluding($phpcsFile, [T_WHITESPACE], $inlineElsePointer - 1);
		$pointerAfterInlineElse = TokenHelper::findNextExcluding($phpcsFile, [T_WHITESPACE], $inlineElsePointer + 1);
		$nextEffectiveAfterInlineElse = TokenHelper::findNextEffective($phpcsFile, $inlineElsePointer + 1);

		$startPointer = IndentationHelper::getExpressionStartPointer($phpcsFile, $inlineThenPointer, [T_INLINE_THEN, T_INLINE_ELSE]);
		$indentation = IndentationHelper::addIndentation(
			$phpcsFile,
			IndentationHelper::getIndentation(
				$phpcsFile,
				TokenHelper::findFirstNonWhitespaceOnLine($phpcsFile, $startPointer),
			),
		);

		$isInlineThenLeading = TokenHelper::findFirstNonWhitespaceOnLine($phpcsFile, $inlineThenPointer) === $inlineThenPointer;
		$isInlineElseLeading = TokenHelper::findFirstNonWhitespaceOnLine($phpcsFile, $inlineElsePointer) === $inlineElsePointer;

		if (!$isInlineThenLeading && $tokens[$inlineThenPointer]['line'] !== $tokens[$nextPointer]['line']) {
			$fix = $phpcsFile->addFixableError(
				'Ternary operator should be reformatted as leading the line.',
				$inlineThenPointer,
				$this->getErrorCode(),
			);

			if (!$fix) {
				return;
			}

			$phpcsFile->fixer->beginChangeset();
			IndentationHelper::reformatOperatorAsLeading(
				$phpcsFile,
				$pointerBeforeInlineThen,
				$inlineThenPointer,
				$pointerAfterInlineThen,
				$indentation,
			);
			IndentationHelper::reformatOperatorAsLeading(
				$phpcsFile,
				$pointerBeforeInlineElse,
				$inlineElsePointer,
				$pointerAfterInlineElse,
				$indentation,
			);
			$phpcsFile->fixer->endChangeset();
			return;
		}

		if ($isInlineThenLeading) {
			$this->checkLeadingOperator($phpcsFile, $inlineThenPointer, $pointerAfterInlineThen, $indentation);

			if (!$isInlineElseLeading && $tokens[$inlineElsePointer]['line'] !== $tokens[$nextEffectiveAfterInlineElse]['line']) {
				$fix = $phpcsFile->addFixableError(
					'Ternary operator should be reformatted as leading the line.',
					$inlineElsePointer,
					$this->getErrorCode(),
				);

				if ($fix) {
					$phpcsFile->fixer->beginChangeset();
					IndentationHelper::reformatOperatorAsLeading(
						$phpcsFile,
						$pointerBeforeInlineElse,
						$inlineElsePointer,
						$pointerAfterInlineElse,
						$indentation,
					);
					$phpcsFile->fixer->endChangeset();
				}

				return;
			}
		}

		if ($isInlineElseLeading) {
			$this->checkLeadingOperator($phpcsFile, $inlineElsePointer, $pointerAfterInlineElse, $indentation);
		}
	}

	/**
	 * Returns the error code reported when a multiline ternary operator is trailing or has invalid indentation.
	 *
	 * @return string The sniff error code.
	 */
	protected function getErrorCode(): string
	{
		return self::CODE_TRAILING_MULTI_LINE_TERNARY_OPERATOR_USED;
	}

	/**
	 * Verifies that a ternary operator ("?" or ":") at the start of a continuation line has the exact
	 * expected indentation before it and a single space after it, reporting and fixing any violation.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param int $operatorPointer The token pointer to the leading ternary operator.
	 * @param int $pointerAfterOperator The token pointer to the first non-whitespace token after the operator.
	 * @param string $expectedIndentation The expected whitespace indentation string before the operator.
	 */
	private function checkLeadingOperator(
		File $phpcsFile,
		int $operatorPointer,
		int $pointerAfterOperator,
		string $expectedIndentation
	): void
	{
		IndentationHelper::checkLeadingOperatorIndentation(
			$phpcsFile,
			$operatorPointer,
			$pointerAfterOperator,
			$expectedIndentation,
			'Ternary operator has invalid indentation.',
			$this->getErrorCode(),
		);
	}

}
