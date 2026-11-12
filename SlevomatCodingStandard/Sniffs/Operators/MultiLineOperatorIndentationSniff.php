<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Operators;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\IndentationHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function array_map;
use function array_merge;
use function constant;
use function count;
use function defined;
use function in_array;
use function is_numeric;
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
use const T_END_HEREDOC;
use const T_END_NOWDOC;
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
use const T_START_HEREDOC;
use const T_START_NOWDOC;
use const T_STRING_CONCAT;
use const T_THROW;
use const T_WHITESPACE;
use const T_XOR_EQUAL;
use const T_YIELD;
use const T_YIELD_FROM;

/**
 * Enforces that configured operators in multiline expressions are placed at the beginning
 * of continuation lines and indented exactly one level deeper than the line where the
 * expression starts, followed by a single space.
 *
 * Also suppresses conflicting indentation errors from Generic.WhiteSpace.ScopeIndent and
 * Squiz.Functions.MultiLineFunctionDeclaration on continuation lines managed by this sniff.
 */
class MultiLineOperatorIndentationSniff implements Sniff
{

	/**
	 * Error code reported when a multiline operator is trailing at the end of a line
	 * or has invalid leading indentation or spacing.
	 */
	public const CODE_INVALID_OPERATOR_INDENTATION = 'InvalidOperatorIndentation';

	/**
	 * Alias for CODE_INVALID_OPERATOR_INDENTATION for multiline operator indentation checks.
	 */
	public const CODE_INVALID_MULTI_LINE_OPERATOR_INDENTATION = self::CODE_INVALID_OPERATOR_INDENTATION;

	/**
	 * Alias for CODE_INVALID_OPERATOR_INDENTATION when a multiline operator is placed at the end of a line.
	 */
	public const CODE_TRAILING_MULTI_LINE_OPERATOR_USED = self::CODE_INVALID_OPERATOR_INDENTATION;

	/**
	 * List of operator token names (e.g. "T_STRING_CONCAT", "T_BOOLEAN_AND") or token codes to check.
	 * When empty, defaults to checking T_STRING_CONCAT (".").
	 *
	 * @var list<int|string>
	 */
	public array $operators = [];

	/**
	 * Registers T_OPEN_TAG (prioritized ahead of Generic.WhiteSpace.ScopeIndent) and all configured
	 * operator tokens to listen for.
	 *
	 * @return array<int, (int|string)> List of token codes handled by this sniff.
	 */
	public function register(): array
	{
		IndentationHelper::prioritizeOpenTagListener($this);

		return array_merge([T_OPEN_TAG], $this->getRegisteredOperators());
	}

	/**
	 * Processes T_OPEN_TAG to register conflict exclusions and processes each configured operator token
	 * to enforce leading operator placement and exact indentation in multiline expressions.
	 *
	 * Expressions containing heredoc or nowdoc tokens between the start of the expression and the
	 * operand following the operator are skipped.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param int $operatorPointer The position of the current token in the token stack.
	 */
	public function process(File $phpcsFile, int $operatorPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$operatorPointer]['code'] === T_OPEN_TAG) {
			$this->ignoreConflictingSniffs($phpcsFile, $operatorPointer);
			return;
		}

		$pointerBeforeOperator = TokenHelper::findPreviousExcluding($phpcsFile, [T_WHITESPACE], $operatorPointer - 1);
		$pointerAfterOperator = TokenHelper::findNextExcluding($phpcsFile, [T_WHITESPACE], $operatorPointer + 1);
		$nextEffectivePointer = TokenHelper::findNextEffective($phpcsFile, $operatorPointer + 1);

		$startPointer = $this->getStartPointer($phpcsFile, $operatorPointer);

		$heredocPointer = TokenHelper::findNext(
			$phpcsFile,
			[T_START_HEREDOC, T_START_NOWDOC, T_END_HEREDOC, T_END_NOWDOC],
			$startPointer,
			$nextEffectivePointer + 1,
		);
		if ($heredocPointer !== null) {
			return;
		}

		$baseIndentation = IndentationHelper::getIndentation(
			$phpcsFile,
			TokenHelper::findFirstNonWhitespaceOnLine($phpcsFile, $startPointer),
		);
		$expectedIndentation = IndentationHelper::addIndentation($phpcsFile, $baseIndentation);

		$isOperatorLeading = TokenHelper::findFirstNonWhitespaceOnLine($phpcsFile, $operatorPointer) === $operatorPointer;

		if (!$isOperatorLeading && $tokens[$operatorPointer]['line'] !== $tokens[$nextEffectivePointer]['line']) {
			$fix = $phpcsFile->addFixableError(
				'Operator should be reformatted as leading the line.',
				$operatorPointer,
				self::CODE_INVALID_OPERATOR_INDENTATION,
			);

			if (!$fix) {
				return;
			}

			$phpcsFile->fixer->beginChangeset();

			FixerHelper::removeBetween($phpcsFile, $pointerBeforeOperator, $operatorPointer);
			FixerHelper::removeBetween($phpcsFile, $operatorPointer, $pointerAfterOperator);

			FixerHelper::addBefore($phpcsFile, $operatorPointer, $phpcsFile->eolChar . $expectedIndentation);
			FixerHelper::addBefore($phpcsFile, $pointerAfterOperator, ' ');

			$phpcsFile->fixer->endChangeset();
			return;
		}

		if ($tokens[$operatorPointer]['line'] <= $tokens[$startPointer]['line'] || !$isOperatorLeading) {
			return;
		}

		$this->checkLeadingOperator($phpcsFile, $operatorPointer, $pointerAfterOperator, $expectedIndentation);
	}

	/**
	 * Normalizes and resolves the configured operator names or token codes into PHP_CodeSniffer token codes.
	 * Defaults to [T_STRING_CONCAT] when no operators are explicitly configured.
	 *
	 * @return list<int|string> Resolved list of operator token codes.
	 */
	private function getRegisteredOperators(): array
	{
		$operators = SniffSettingsHelper::normalizeArray(
			array_map(static fn ($operator): string => (string) $operator, $this->operators),
		);
		if (count($operators) === 0) {
			return [
				T_STRING_CONCAT,
			];
		}

		/** @var list<int|string> $registeredOperators */
		$registeredOperators = array_map(
			static fn (string $operator) => is_numeric($operator)
				? (int) $operator
				: (defined($operator) ? constant($operator) : $operator),
			$operators,
		);

		return $registeredOperators;
	}

	/**
	 * Configures Generic.WhiteSpace.ScopeIndent and line-level ignore rules on T_OPEN_TAG
	 * so that continuation lines managed by this sniff do not trigger conflicting errors
	 * from Generic.WhiteSpace.ScopeIndent or Squiz.Functions.MultiLineFunctionDeclaration.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param int $openTagPointer The token pointer of the T_OPEN_TAG token.
	 */
	private function ignoreConflictingSniffs(File $phpcsFile, int $openTagPointer): void
	{
		$registeredOperators = $this->getRegisteredOperators();

		IndentationHelper::ignoreTokensInScopeIndent($phpcsFile, $registeredOperators);

		$tokens = $phpcsFile->getTokens();
		foreach (TokenHelper::findNextAll($phpcsFile, $registeredOperators, $openTagPointer + 1) as $operatorPointer) {
			$nextEffectivePointer = TokenHelper::findNextEffective($phpcsFile, $operatorPointer + 1);
			$startPointer = $this->getStartPointer($phpcsFile, $operatorPointer);

			$heredocPointer = TokenHelper::findNext(
				$phpcsFile,
				[T_START_HEREDOC, T_START_NOWDOC, T_END_HEREDOC, T_END_NOWDOC],
				$startPointer,
				$nextEffectivePointer + 1,
			);
			if ($heredocPointer !== null) {
				continue;
			}

			if ($tokens[$operatorPointer]['line'] !== $tokens[$nextEffectivePointer]['line']) {
				IndentationHelper::ignoreConflictingIndentationSniffsOnLine($phpcsFile, $tokens[$nextEffectivePointer]['line']);
			}

			if (
				$tokens[$operatorPointer]['line'] > $tokens[$startPointer]['line']
				&& TokenHelper::findFirstNonWhitespaceOnLine($phpcsFile, $operatorPointer) === $operatorPointer
			) {
				IndentationHelper::ignoreConflictingIndentationSniffsOnLine($phpcsFile, $tokens[$operatorPointer]['line']);
			}

			$pointerBeforeStart = TokenHelper::findPreviousEffective($phpcsFile, $startPointer - 1);
			if (
				$pointerBeforeStart !== null
				&& in_array($tokens[$pointerBeforeStart]['code'], [T_FN_ARROW, T_MATCH_ARROW], true)
				&& $tokens[$startPointer]['line'] > $tokens[$pointerBeforeStart]['line']
			) {
				IndentationHelper::ignoreConflictingIndentationSniffsOnLine($phpcsFile, $tokens[$startPointer]['line']);
			}
		}
	}

	/**
	 * Verifies that an operator at the start of a continuation line has the exact expected indentation
	 * before it and a single space after it, reporting and fixing any violation.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param int $operatorPointer The token pointer to the leading operator.
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

		$fix = $phpcsFile->addFixableError('Operator has invalid indentation.', $operatorPointer, self::CODE_INVALID_OPERATOR_INDENTATION);

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
	 * Finds the token pointer where the current expression containing the operator starts,
	 * skipping over balanced parentheses, brackets, and curly braces that precede the operator.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param int $operatorPointer The token pointer to the operator.
	 * @return int The token pointer to the first effective token of the expression.
	 */
	private function getStartPointer(File $phpcsFile, int $operatorPointer): int
	{
		$tokens = $phpcsFile->getTokens();

		$pointerBeforeExpression = $operatorPointer;
		do {
			$pointerBeforeExpression = TokenHelper::findPrevious(
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
				$pointerBeforeExpression - 1,
			);

			if (
				in_array(
					$tokens[$pointerBeforeExpression]['code'],
					[T_CLOSE_SQUARE_BRACKET, T_CLOSE_SHORT_ARRAY],
					true,
				)
			) {
				$pointerBeforeExpression = $tokens[$pointerBeforeExpression]['bracket_opener'];
				continue;
			}

			if ($tokens[$pointerBeforeExpression]['code'] === T_CLOSE_CURLY_BRACKET) {
				$scopeOpener = $tokens[$pointerBeforeExpression]['scope_opener'] ?? $tokens[$pointerBeforeExpression]['bracket_opener'] ?? null;
				if ($scopeOpener !== null) {
					$scopeCondition = $tokens[$pointerBeforeExpression]['scope_condition'] ?? $tokens[$scopeOpener]['scope_condition'] ?? null;
					if (
						$scopeCondition !== null
						&& in_array($tokens[$scopeCondition]['code'], [T_CLOSURE, T_ANON_CLASS, T_MATCH], true)
					) {
						$pointerBeforeExpression = $scopeCondition;
						continue;
					}

					$pointerBeforeExpression = $scopeOpener;
					continue;
				}
			}

			if ($tokens[$pointerBeforeExpression]['code'] === T_CLOSE_PARENTHESIS) {
				$pointerBeforeExpression = $tokens[$pointerBeforeExpression]['parenthesis_opener'];
				continue;
			}

			break;

		} while (true);

		return TokenHelper::findNextEffective($phpcsFile, $pointerBeforeExpression + 1);
	}

}
