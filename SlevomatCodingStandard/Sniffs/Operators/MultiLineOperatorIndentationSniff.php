<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Operators;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
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
use const T_END_HEREDOC;
use const T_END_NOWDOC;
use const T_FN_ARROW;
use const T_INLINE_ELSE;
use const T_INLINE_THEN;
use const T_MATCH_ARROW;
use const T_OPEN_TAG;
use const T_START_HEREDOC;
use const T_START_NOWDOC;
use const T_STRING_CONCAT;
use const T_WHITESPACE;

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

		if ($this->hasHeredoc($phpcsFile, $startPointer, $nextEffectivePointer)) {
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
			IndentationHelper::reformatOperatorAsLeading(
				$phpcsFile,
				$pointerBeforeOperator,
				$operatorPointer,
				$pointerAfterOperator,
				$expectedIndentation,
			);
			$phpcsFile->fixer->endChangeset();
			return;
		}

		if ($tokens[$operatorPointer]['line'] <= $tokens[$startPointer]['line'] || !$isOperatorLeading) {
			return;
		}

		IndentationHelper::checkLeadingOperatorIndentation(
			$phpcsFile,
			$operatorPointer,
			$pointerAfterOperator,
			$expectedIndentation,
			'Operator has invalid indentation.',
			self::CODE_INVALID_OPERATOR_INDENTATION,
		);

		if (
			$tokens[$operatorPointer]['line'] === $tokens[$nextEffectivePointer]['line']
			|| TokenHelper::findFirstNonWhitespaceOnLine($phpcsFile, $nextEffectivePointer) !== $nextEffectivePointer
			|| IndentationHelper::getIndentation($phpcsFile, $nextEffectivePointer) === $expectedIndentation
		) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'Operand after operator has invalid indentation.',
			$nextEffectivePointer,
			self::CODE_INVALID_OPERATOR_INDENTATION,
		);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();
		IndentationHelper::fixLineIndentation($phpcsFile, $nextEffectivePointer, $expectedIndentation);
		$phpcsFile->fixer->endChangeset();
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

		$tokens = $phpcsFile->getTokens();
		foreach (TokenHelper::findNextAll($phpcsFile, $registeredOperators, $openTagPointer + 1) as $operatorPointer) {
			$nextEffectivePointer = TokenHelper::findNextEffective($phpcsFile, $operatorPointer + 1);
			$startPointer = $this->getStartPointer($phpcsFile, $operatorPointer);

			if (
				$tokens[$operatorPointer]['line'] === $tokens[$startPointer]['line']
				&& $tokens[$operatorPointer]['line'] === $tokens[$nextEffectivePointer]['line']
			) {
				continue;
			}

			if ($this->hasHeredoc($phpcsFile, $startPointer, $nextEffectivePointer)) {
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
				$pointerBeforeStart === null
				|| !in_array($tokens[$pointerBeforeStart]['code'], [T_FN_ARROW, T_MATCH_ARROW], true)
				|| $tokens[$startPointer]['line'] <= $tokens[$pointerBeforeStart]['line']
			) {
				continue;
			}

			$arrowIndentation = IndentationHelper::getIndentation(
				$phpcsFile,
				TokenHelper::findFirstNonWhitespaceOnLine($phpcsFile, $pointerBeforeStart),
			);
			$startIndentation = IndentationHelper::getIndentation(
				$phpcsFile,
				TokenHelper::findFirstNonWhitespaceOnLine($phpcsFile, $startPointer),
			);

			if ($startIndentation === IndentationHelper::addIndentation($phpcsFile, $arrowIndentation)) {
				IndentationHelper::ignoreConflictingIndentationSniffsOnLine($phpcsFile, $tokens[$startPointer]['line']);
			}
		}
	}

	/**
	 * Determines whether a heredoc or nowdoc boundary token appears between the given start and end pointers.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param int $startPointer The token pointer where the range starts.
	 * @param int $endPointer The token pointer where the range ends (inclusive).
	 * @return bool True if a heredoc or nowdoc token is present in the range, false otherwise.
	 */
	private function hasHeredoc(File $phpcsFile, int $startPointer, int $endPointer): bool
	{
		return TokenHelper::findNext(
			$phpcsFile,
			[T_START_HEREDOC, T_START_NOWDOC, T_END_HEREDOC, T_END_NOWDOC],
			$startPointer,
			$endPointer + 1,
		) !== null;
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
		return IndentationHelper::getExpressionStartPointer($phpcsFile, $operatorPointer, [T_INLINE_THEN, T_INLINE_ELSE]);
	}

}
