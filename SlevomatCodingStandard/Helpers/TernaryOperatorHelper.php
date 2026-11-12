<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use function array_merge;
use function array_values;
use function count;
use function in_array;
use const T_ANON_CLASS;
use const T_CASE;
use const T_CLOSE_CURLY_BRACKET;
use const T_CLOSE_PARENTHESIS;
use const T_CLOSE_SHORT_ARRAY;
use const T_CLOSE_SQUARE_BRACKET;
use const T_CLOSE_TAG;
use const T_CLOSURE;
use const T_COLON;
use const T_COMMA;
use const T_DOUBLE_ARROW;
use const T_ECHO;
use const T_FN;
use const T_FN_ARROW;
use const T_INLINE_ELSE;
use const T_INLINE_THEN;
use const T_LOGICAL_AND;
use const T_LOGICAL_OR;
use const T_LOGICAL_XOR;
use const T_MATCH;
use const T_MATCH_ARROW;
use const T_OPEN_CURLY_BRACKET;
use const T_OPEN_PARENTHESIS;
use const T_OPEN_SHORT_ARRAY;
use const T_OPEN_SQUARE_BRACKET;
use const T_OPEN_TAG;
use const T_OPEN_TAG_WITH_ECHO;
use const T_PRINT;
use const T_RETURN;
use const T_SEMICOLON;
use const T_THROW;
use const T_WHITESPACE;
use const T_YIELD;
use const T_YIELD_FROM;

/**
 * Provides helper methods for locating boundaries and tokens of ternary operator expressions
 * and coordinating indentation conflict exclusions for multiline ternaries.
 *
 * @internal
 */
class TernaryOperatorHelper
{

	/**
	 * Determines whether the given T_INLINE_THEN pointer represents a ternary operator rather than
	 * a nullable type hint directly following an opening parenthesis.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param int $pointer The token pointer to check.
	 * @return bool True if the token is a ternary operator, false otherwise.
	 */
	public static function is(File $phpcsFile, int $pointer): bool
	{
		$previousPointer = TokenHelper::findPreviousEffective($phpcsFile, $pointer - 1);

		return $phpcsFile->getTokens()[$previousPointer]['code'] !== T_OPEN_PARENTHESIS;
	}

	/**
	 * Determines whether the given token pointer is part of the condition expression of a subsequent
	 * ternary operator.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param int $pointer The token pointer to check.
	 * @return bool True if the token is within the condition of a ternary operator, false otherwise.
	 */
	public static function isConditionOfTernaryOperator(File $phpcsFile, int $pointer): bool
	{
		$inlineThenPointer = TokenHelper::findNext($phpcsFile, T_INLINE_THEN, $pointer + 1);

		if ($inlineThenPointer === null) {
			return false;
		}

		$startPointer = self::getStartPointer($phpcsFile, $inlineThenPointer);

		return $startPointer <= $pointer;
	}

	/**
	 * Finds the matching T_INLINE_ELSE (":") token pointer for the given T_INLINE_THEN ("?") token pointer
	 * in the same scope, skipping over nested parentheses and brackets.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param int $inlineThenPointer The token pointer to the T_INLINE_THEN ("?") token.
	 * @return int The token pointer to the matching T_INLINE_ELSE (":") token.
	 */
	public static function getElsePointer(File $phpcsFile, int $inlineThenPointer): int
	{
		$tokens = $phpcsFile->getTokens();

		$pointer = $inlineThenPointer;
		do {
			$pointer = TokenHelper::findNext(
				$phpcsFile,
				[T_INLINE_THEN, T_INLINE_ELSE, T_OPEN_PARENTHESIS, T_OPEN_SHORT_ARRAY, T_OPEN_SQUARE_BRACKET],
				$pointer + 1,
			);

			if ($tokens[$pointer]['code'] === T_OPEN_PARENTHESIS) {
				$pointer = $tokens[$pointer]['parenthesis_closer'];
				continue;
			}

			if (in_array($tokens[$pointer]['code'], [T_OPEN_SHORT_ARRAY, T_OPEN_SQUARE_BRACKET], true)) {
				$pointer = $tokens[$pointer]['bracket_closer'];
				continue;
			}

			if ($tokens[$pointer]['code'] === T_INLINE_THEN && ScopeHelper::isInSameScope($phpcsFile, $inlineThenPointer, $pointer)) {
				$pointer = self::getElsePointer($phpcsFile, $pointer);
				continue;
			}

			if (ScopeHelper::isInSameScope($phpcsFile, $inlineThenPointer, $pointer)) {
				break;
			}
		} while ($pointer !== null);

		return $pointer;
	}

	/**
	 * Finds the first effective token pointer of the condition expression for the given T_INLINE_THEN ("?")
	 * token pointer, skipping backward over balanced parentheses and square/short-array brackets.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param int $inlineThenPointer The token pointer to the T_INLINE_THEN ("?") token.
	 * @return int The token pointer where the ternary condition expression starts.
	 */
	public static function getStartPointer(File $phpcsFile, int $inlineThenPointer): int
	{
		$tokens = $phpcsFile->getTokens();

		$pointerBeforeCondition = $inlineThenPointer;
		do {
			$pointerBeforeCondition = TokenHelper::findPrevious(
				$phpcsFile,
				array_merge(
					[
						T_RETURN,
						T_THROW,
						T_YIELD,
						T_YIELD_FROM,
						T_ECHO,
						T_PRINT,
						T_CASE,
						T_OPEN_TAG,
						T_OPEN_TAG_WITH_ECHO,
						T_DOUBLE_ARROW,
						T_FN_ARROW,
						T_MATCH_ARROW,
						T_COLON,
						T_OPEN_SQUARE_BRACKET,
						T_OPEN_SHORT_ARRAY,
						T_OPEN_PARENTHESIS,
						T_CLOSE_PARENTHESIS,
						T_CLOSE_SHORT_ARRAY,
						T_CLOSE_SQUARE_BRACKET,
						T_CLOSE_CURLY_BRACKET,
						T_COMMA,
					],
					array_values(Tokens::ASSIGNMENT_TOKENS),
				),
				$pointerBeforeCondition - 1,
			);

			if ($tokens[$pointerBeforeCondition]['code'] === T_CLOSE_PARENTHESIS) {
				$pointerBeforeCondition = $tokens[$pointerBeforeCondition]['parenthesis_opener'];
				continue;
			}

			if (in_array($tokens[$pointerBeforeCondition]['code'], [T_CLOSE_SHORT_ARRAY, T_CLOSE_SQUARE_BRACKET], true)) {
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

			break;

		} while (true);

		return TokenHelper::findNextEffective($phpcsFile, $pointerBeforeCondition + 1);
	}

	/**
	 * Finds the last effective token pointer of the false-branch expression following the given
	 * T_INLINE_ELSE (":") token pointer.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param int $inlineThenPointer The token pointer to the T_INLINE_THEN ("?") token.
	 * @param int $inlineElsePointer The token pointer to the matching T_INLINE_ELSE (":") token.
	 * @return int The token pointer where the ternary expression ends.
	 */
	public static function getEndPointer(File $phpcsFile, int $inlineThenPointer, int $inlineElsePointer): int
	{
		$tokens = $phpcsFile->getTokens();

		$pointerAfterInlineElseEnd = $inlineElsePointer;
		do {
			$pointerAfterInlineElseEnd = TokenHelper::findNext(
				$phpcsFile,
				[
					T_SEMICOLON,
					T_CLOSE_TAG,
					T_COLON,
					T_COMMA,
					T_DOUBLE_ARROW,
					T_MATCH_ARROW,
					T_CLOSE_PARENTHESIS,
					T_CLOSE_SHORT_ARRAY,
					T_CLOSE_SQUARE_BRACKET,
					T_CLOSE_CURLY_BRACKET,
					T_OPEN_PARENTHESIS,
					T_OPEN_SHORT_ARRAY,
					T_OPEN_SQUARE_BRACKET,
					T_OPEN_CURLY_BRACKET,
					T_INLINE_THEN,
					T_LOGICAL_AND,
					T_LOGICAL_OR,
					T_LOGICAL_XOR,
					T_CLOSURE,
					T_ANON_CLASS,
					T_FN,
				],
				$pointerAfterInlineElseEnd + 1,
			);

			if ($pointerAfterInlineElseEnd === null) {
				continue;
			}

			if (
				in_array($tokens[$pointerAfterInlineElseEnd]['code'], [T_CLOSURE, T_ANON_CLASS], true)
				&& isset($tokens[$pointerAfterInlineElseEnd]['scope_closer'])
			) {
				// Skip the whole signature (including a return type colon) and body
				$pointerAfterInlineElseEnd = $tokens[$pointerAfterInlineElseEnd]['scope_closer'];
				continue;
			}

			if ($tokens[$pointerAfterInlineElseEnd]['code'] === T_FN) {
				if (isset($tokens[$pointerAfterInlineElseEnd]['scope_closer'])) {
					// The arrow function closer is the token terminating its expression, so it has to be inspected again
					$pointerAfterInlineElseEnd = $tokens[$pointerAfterInlineElseEnd]['scope_closer'] - 1;
				}
				continue;
			}

			if ($tokens[$pointerAfterInlineElseEnd]['code'] === T_OPEN_PARENTHESIS) {
				$pointerAfterInlineElseEnd = $tokens[$pointerAfterInlineElseEnd]['parenthesis_closer'];
				continue;
			}

			if (in_array($tokens[$pointerAfterInlineElseEnd]['code'], [T_OPEN_SHORT_ARRAY, T_OPEN_SQUARE_BRACKET], true)) {
				$pointerAfterInlineElseEnd = $tokens[$pointerAfterInlineElseEnd]['bracket_closer'];
				continue;
			}

			if (
				$tokens[$pointerAfterInlineElseEnd]['code'] === T_OPEN_CURLY_BRACKET
				&& isset($tokens[$pointerAfterInlineElseEnd]['bracket_closer'])
			) {
				$pointerAfterInlineElseEnd = $tokens[$pointerAfterInlineElseEnd]['bracket_closer'];
				continue;
			}

			if ($tokens[$pointerAfterInlineElseEnd]['code'] === T_CLOSE_PARENTHESIS) {
				if ($tokens[$pointerAfterInlineElseEnd]['parenthesis_opener'] < $inlineThenPointer) {
					break;
				}
			} elseif (in_array($tokens[$pointerAfterInlineElseEnd]['code'], [T_CLOSE_SHORT_ARRAY, T_CLOSE_SQUARE_BRACKET], true)) {
				if ($tokens[$pointerAfterInlineElseEnd]['bracket_opener'] < $inlineThenPointer) {
					break;
				}
			} elseif ($tokens[$pointerAfterInlineElseEnd]['code'] === T_CLOSE_CURLY_BRACKET) {
				$opener = $tokens[$pointerAfterInlineElseEnd]['bracket_opener'] ?? $tokens[$pointerAfterInlineElseEnd]['scope_opener'] ?? null;
				if ($opener === null || $opener < $inlineThenPointer) {
					break;
				}
			} elseif ($tokens[$pointerAfterInlineElseEnd]['code'] === T_MATCH_ARROW) {
				break;
			} elseif ($tokens[$pointerAfterInlineElseEnd]['code'] === T_COMMA) {
				$previousPointer = TokenHelper::findPrevious(
					$phpcsFile,
					[T_OPEN_PARENTHESIS, T_OPEN_SHORT_ARRAY, T_OPEN_SQUARE_BRACKET],
					$pointerAfterInlineElseEnd - 1,
					$inlineThenPointer,
				);

				if ($previousPointer === null) {
					break;
				}

				if (
					$tokens[$previousPointer]['code'] === T_OPEN_PARENTHESIS
					&& $tokens[$previousPointer]['parenthesis_closer'] < $pointerAfterInlineElseEnd
				) {
					break;
				}

				if (
					in_array($tokens[$previousPointer]['code'], [T_OPEN_SHORT_ARRAY, T_OPEN_SQUARE_BRACKET], true)
					&& $tokens[$previousPointer]['bracket_closer'] < $pointerAfterInlineElseEnd
				) {
					break;
				}

			} elseif ($tokens[$pointerAfterInlineElseEnd]['code'] === T_DOUBLE_ARROW) {
				$previousPointer = TokenHelper::findPrevious(
					$phpcsFile,
					[T_OPEN_PARENTHESIS, T_OPEN_SHORT_ARRAY, T_OPEN_SQUARE_BRACKET],
					$pointerAfterInlineElseEnd - 1,
					$inlineThenPointer,
				);
				if ($previousPointer === null) {
					break;
				}

				if (
					$tokens[$previousPointer]['code'] === T_OPEN_PARENTHESIS
					&& $tokens[$previousPointer]['parenthesis_closer'] < $pointerAfterInlineElseEnd
				) {
					break;
				}

				if (
					in_array($tokens[$previousPointer]['code'], [T_OPEN_SHORT_ARRAY, T_OPEN_SQUARE_BRACKET], true)
					&& $tokens[$previousPointer]['bracket_closer'] < $pointerAfterInlineElseEnd
				) {
					break;
				}
			} elseif (ScopeHelper::isInSameScope($phpcsFile, $inlineElsePointer, $pointerAfterInlineElseEnd)) {
				break;
			}
		} while ($pointerAfterInlineElseEnd !== null);

		if ($pointerAfterInlineElseEnd !== null) {
			return TokenHelper::findPreviousEffective($phpcsFile, $pointerAfterInlineElseEnd - 1);
		}

		return TokenHelper::findPreviousEffective($phpcsFile, count($tokens) - 1);
	}

	/**
	 * Marks continuation lines of a multiline ternary expression (both "?" and ":") in the file's
	 * tokenizer ignore list so that external indentation sniffs do not conflict with ternary
	 * operator indentation rules. Short ternary operators ("?:") are skipped.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param int $inlineThenPointer The token pointer to the T_INLINE_THEN ("?") token.
	 */
	public static function ignoreConflictingIndentationSniffs(File $phpcsFile, int $inlineThenPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$nextPointer = TokenHelper::findNextEffective($phpcsFile, $inlineThenPointer + 1);
		if ($tokens[$nextPointer]['code'] === T_INLINE_ELSE) {
			return;
		}

		$inlineElsePointer = self::getElsePointer($phpcsFile, $inlineThenPointer);
		$pointerAfterInlineElse = TokenHelper::findNextExcluding($phpcsFile, [T_WHITESPACE], $inlineElsePointer + 1);
		$nextEffectiveAfterInlineElse = TokenHelper::findNextEffective($phpcsFile, $inlineElsePointer + 1);

		$isInlineThenLeading = TokenHelper::findFirstNonWhitespaceOnLine($phpcsFile, $inlineThenPointer) === $inlineThenPointer;
		$isInlineElseLeading = TokenHelper::findFirstNonWhitespaceOnLine($phpcsFile, $inlineElsePointer) === $inlineElsePointer;

		if (!$isInlineThenLeading && $tokens[$inlineThenPointer]['line'] !== $tokens[$nextPointer]['line']) {
			IndentationHelper::ignoreConflictingIndentationSniffsOnLine($phpcsFile, $tokens[$nextPointer]['line']);
			IndentationHelper::ignoreConflictingIndentationSniffsOnLine($phpcsFile, $tokens[$pointerAfterInlineElse]['line']);
			return;
		}

		if ($isInlineThenLeading) {
			IndentationHelper::ignoreConflictingIndentationSniffsOnLine($phpcsFile, $tokens[$inlineThenPointer]['line']);

			if (!$isInlineElseLeading && $tokens[$inlineElsePointer]['line'] !== $tokens[$nextEffectiveAfterInlineElse]['line']) {
				IndentationHelper::ignoreConflictingIndentationSniffsOnLine($phpcsFile, $tokens[$nextEffectiveAfterInlineElse]['line']);
				return;
			}
		}

		if ($isInlineElseLeading) {
			IndentationHelper::ignoreConflictingIndentationSniffsOnLine($phpcsFile, $tokens[$inlineElsePointer]['line']);
		}
	}

	/**
	 * Determines whether the expression between startPointer and endPointer contains an unparenthesized
	 * ternary operator at the top expression level.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param int $startPointer The token pointer to the start of the expression.
	 * @param int $endPointer The token pointer to the end of the expression.
	 * @return bool True if an unparenthesized ternary operator is found, false otherwise.
	 */
	public static function hasUnparenthesizedTernary(File $phpcsFile, int $startPointer, int $endPointer): bool
	{
		$tokens = $phpcsFile->getTokens();

		for ($i = $startPointer; $i <= $endPointer; $i++) {
			if ($tokens[$i]['code'] === T_OPEN_PARENTHESIS) {
				$i = $tokens[$i]['parenthesis_closer'];
				continue;
			}

			if (in_array($tokens[$i]['code'], [T_OPEN_SHORT_ARRAY, T_OPEN_SQUARE_BRACKET], true)) {
				$i = $tokens[$i]['bracket_closer'];
				continue;
			}

			if ($tokens[$i]['code'] === T_OPEN_CURLY_BRACKET && isset($tokens[$i]['bracket_closer'])) {
				$i = $tokens[$i]['bracket_closer'];
				continue;
			}

			if ($tokens[$i]['code'] === T_INLINE_THEN) {
				return true;
			}
		}

		return false;
	}

}
