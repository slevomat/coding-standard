<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\IndentationHelper;
use SlevomatCodingStandard\Helpers\TernaryOperatorHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function in_array;
use const T_AND_EQUAL;
use const T_ANON_CLASS;
use const T_CASE;
use const T_CLOSE_CURLY_BRACKET;
use const T_CLOSE_PARENTHESIS;
use const T_CLOSE_SHORT_ARRAY;
use const T_CLOSE_SQUARE_BRACKET;
use const T_CLOSURE;
use const T_COALESCE_EQUAL;
use const T_COLON;
use const T_COMMA;
use const T_CONCAT_EQUAL;
use const T_DIV_EQUAL;
use const T_DOUBLE_ARROW;
use const T_ECHO;
use const T_EQUAL;
use const T_FN_ARROW;
use const T_INLINE_ELSE;
use const T_INLINE_THEN;
use const T_MATCH;
use const T_MATCH_ARROW;
use const T_MINUS_EQUAL;
use const T_MOD_EQUAL;
use const T_MUL_EQUAL;
use const T_OPEN_CURLY_BRACKET;
use const T_OPEN_PARENTHESIS;
use const T_OPEN_SHORT_ARRAY;
use const T_OPEN_SQUARE_BRACKET;
use const T_OPEN_TAG;
use const T_OPEN_TAG_WITH_ECHO;
use const T_OR_EQUAL;
use const T_PLUS_EQUAL;
use const T_POW_EQUAL;
use const T_PRINT;
use const T_RETURN;
use const T_SEMICOLON;
use const T_SL_EQUAL;
use const T_SR_EQUAL;
use const T_THROW;
use const T_WHITESPACE;
use const T_XOR_EQUAL;
use const T_YIELD;
use const T_YIELD_FROM;

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
			IndentationHelper::ignoreTokensInScopeIndent($phpcsFile, [T_INLINE_THEN, T_INLINE_ELSE]);
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

		$startPointer = $this->getStartPointer($phpcsFile, $inlineThenPointer);
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

			FixerHelper::removeBetween($phpcsFile, $pointerBeforeInlineThen, $inlineThenPointer);
			FixerHelper::removeBetween($phpcsFile, $inlineThenPointer, $pointerAfterInlineThen);

			FixerHelper::addBefore($phpcsFile, $inlineThenPointer, $phpcsFile->eolChar . $indentation);
			FixerHelper::addBefore($phpcsFile, $pointerAfterInlineThen, ' ');

			FixerHelper::removeBetween($phpcsFile, $pointerBeforeInlineElse, $inlineElsePointer);
			FixerHelper::removeBetween($phpcsFile, $inlineElsePointer, $pointerAfterInlineElse);

			FixerHelper::addBefore($phpcsFile, $inlineElsePointer, $phpcsFile->eolChar . $indentation);
			FixerHelper::addBefore($phpcsFile, $pointerAfterInlineElse, ' ');

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

					FixerHelper::removeBetween($phpcsFile, $pointerBeforeInlineElse, $inlineElsePointer);
					FixerHelper::removeBetween($phpcsFile, $inlineElsePointer, $pointerAfterInlineElse);

					FixerHelper::addBefore($phpcsFile, $inlineElsePointer, $phpcsFile->eolChar . $indentation);
					FixerHelper::addBefore($phpcsFile, $pointerAfterInlineElse, ' ');

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
		$tokens = $phpcsFile->getTokens();
		$actualIndentation = IndentationHelper::getIndentation($phpcsFile, $operatorPointer);
		$hasSingleSpaceAfter = $pointerAfterOperator === $operatorPointer + 2 && $tokens[$operatorPointer + 1]['content'] === ' ';

		if ($actualIndentation === $expectedIndentation && $hasSingleSpaceAfter) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'Ternary operator has invalid indentation.',
			$operatorPointer,
			$this->getErrorCode(),
		);

		if (!$fix) {
			return;
		}

		$firstTokenOnLine = TokenHelper::findFirstTokenOnLine($phpcsFile, $operatorPointer);

		$phpcsFile->fixer->beginChangeset();

		if ($firstTokenOnLine < $operatorPointer) {
			FixerHelper::removeBetweenIncluding($phpcsFile, $firstTokenOnLine, $operatorPointer - 1);
		}

		FixerHelper::addBefore($phpcsFile, $operatorPointer, $expectedIndentation);
		FixerHelper::removeBetween($phpcsFile, $operatorPointer, $pointerAfterOperator);
		FixerHelper::addBefore($phpcsFile, $pointerAfterOperator, ' ');

		$phpcsFile->fixer->endChangeset();
	}

	/**
	 * Finds the token pointer where the current ternary condition expression starts,
	 * skipping backward over balanced parentheses, brackets, and curly braces.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param int $inlineThenPointer The token pointer to the T_INLINE_THEN ("?") token.
	 * @return int The token pointer to the first effective token of the condition expression.
	 */
	private function getStartPointer(File $phpcsFile, int $inlineThenPointer): int
	{
		$tokens = $phpcsFile->getTokens();

		$pointerBeforeCondition = $inlineThenPointer;
		do {
			$pointerBeforeCondition = TokenHelper::findPrevious(
				$phpcsFile,
				[
					T_EQUAL,
					T_CONCAT_EQUAL,
					T_COALESCE_EQUAL,
					T_PLUS_EQUAL,
					T_MINUS_EQUAL,
					T_MUL_EQUAL,
					T_DIV_EQUAL,
					T_MOD_EQUAL,
					T_POW_EQUAL,
					T_AND_EQUAL,
					T_OR_EQUAL,
					T_XOR_EQUAL,
					T_SL_EQUAL,
					T_SR_EQUAL,
					T_DOUBLE_ARROW,
					T_FN_ARROW,
					T_MATCH_ARROW,
					T_COMMA,
					T_RETURN,
					T_THROW,
					T_YIELD,
					T_YIELD_FROM,
					T_ECHO,
					T_PRINT,
					T_CASE,
					T_COLON,
					T_SEMICOLON,
					T_INLINE_THEN,
					T_INLINE_ELSE,
					T_OPEN_TAG,
					T_OPEN_TAG_WITH_ECHO,
					T_OPEN_SQUARE_BRACKET,
					T_OPEN_SHORT_ARRAY,
					T_OPEN_PARENTHESIS,
					T_OPEN_CURLY_BRACKET,
					T_CLOSE_SQUARE_BRACKET,
					T_CLOSE_SHORT_ARRAY,
					T_CLOSE_PARENTHESIS,
					T_CLOSE_CURLY_BRACKET,
				],
				$pointerBeforeCondition - 1,
			);

			if (
				in_array(
					$tokens[$pointerBeforeCondition]['code'],
					[T_CLOSE_SQUARE_BRACKET, T_CLOSE_SHORT_ARRAY],
					true,
				)
			) {
				$pointerBeforeCondition = $tokens[$pointerBeforeCondition]['bracket_opener'];
				continue;
			}

			if ($tokens[$pointerBeforeCondition]['code'] === T_CLOSE_CURLY_BRACKET) {
				$scopeOpener = $tokens[$pointerBeforeCondition]['scope_opener'] ?? $tokens[$pointerBeforeCondition]['bracket_opener'] ?? null;
				if ($scopeOpener !== null) {
					$scopeCondition = $tokens[$pointerBeforeCondition]['scope_condition'] ?? $tokens[$scopeOpener]['scope_condition'] ?? null;
					if (
						$scopeCondition !== null
						&& in_array($tokens[$scopeCondition]['code'], [T_CLOSURE, T_ANON_CLASS, T_MATCH], true)
					) {
						$pointerBeforeCondition = $scopeCondition;
						continue;
					}

					$pointerBeforeCondition = $scopeOpener;
					continue;
				}
			}

			if ($tokens[$pointerBeforeCondition]['code'] === T_CLOSE_PARENTHESIS) {
				$pointerBeforeCondition = $tokens[$pointerBeforeCondition]['parenthesis_opener'];
				continue;
			}

			break;

		} while (true);

		return TokenHelper::findNextEffective($phpcsFile, $pointerBeforeCondition + 1);
	}

}
