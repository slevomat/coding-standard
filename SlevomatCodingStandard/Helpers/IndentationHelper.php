<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\IgnoreList;
use PHP_CodeSniffer\Util\Tokens;
use function array_merge;
use function array_values;
use function debug_backtrace;
use function floor;
use function get_class;
use function in_array;
use function ltrim;
use function preg_replace_callback;
use function rtrim;
use function str_repeat;
use function strlen;
use function substr;
use const DEBUG_BACKTRACE_PROVIDE_OBJECT;
use const T_ANON_CLASS;
use const T_CASE;
use const T_CLOSE_CURLY_BRACKET;
use const T_CLOSE_PARENTHESIS;
use const T_CLOSE_SHORT_ARRAY;
use const T_CLOSE_SQUARE_BRACKET;
use const T_CLOSURE;
use const T_COLON;
use const T_COMMA;
use const T_DOUBLE_ARROW;
use const T_ECHO;
use const T_END_HEREDOC;
use const T_END_NOWDOC;
use const T_FN_ARROW;
use const T_INCLUDE;
use const T_INCLUDE_ONCE;
use const T_MATCH;
use const T_MATCH_ARROW;
use const T_OPEN_CURLY_BRACKET;
use const T_OPEN_PARENTHESIS;
use const T_OPEN_SHORT_ARRAY;
use const T_OPEN_SQUARE_BRACKET;
use const T_OPEN_TAG;
use const T_OPEN_TAG_WITH_ECHO;
use const T_PRINT;
use const T_REQUIRE;
use const T_REQUIRE_ONCE;
use const T_RETURN;
use const T_SEMICOLON;
use const T_START_HEREDOC;
use const T_START_NOWDOC;
use const T_THROW;
use const T_YIELD;
use const T_YIELD_FROM;

/**
 * Provides helper methods for inspecting, manipulating, and coordinating line indentation
 * across tokens and sniffs.
 *
 * @internal
 */
class IndentationHelper
{

	/**
	 * Default tab width (in spaces) used when PHP_CodeSniffer config tabWidth is 0.
	 */
	public const DEFAULT_INDENTATION_WIDTH = 4;

	/**
	 * Sniff error codes from external standards that conflict with multiline operator
	 * continuation line indentation.
	 */
	private const CONFLICTING_INDENTATION_SNIFF_CODES = [
		'Generic.WhiteSpace.ScopeIndent.Incorrect',
		'Generic.WhiteSpace.ScopeIndent.IncorrectExact',
		'PEAR.Functions.FunctionDeclaration.Indent',
		'Squiz.Functions.MultiLineFunctionDeclaration.Indent',
	];

	/**
	 * Places the given sniff instance's class at the front of the Ruleset's T_OPEN_TAG token listeners
	 * when called from Ruleset::populateTokenListeners(), ensuring it runs before
	 * Generic.WhiteSpace.ScopeIndent.
	 *
	 * @param Sniff $sniff The sniff instance to prioritize.
	 */
	public static function prioritizeOpenTagListener(Sniff $sniff): void
	{
		$sniffClass = get_class($sniff);
		$backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 3);
		foreach ($backtrace as $frame) {
			$object = $frame['object'] ?? null;
			if ($object instanceof Ruleset) {
				$openTagListeners = $object->tokenListeners[T_OPEN_TAG] ?? [];
				$object->tokenListeners[T_OPEN_TAG] = [$sniffClass => null] + $openTagListeners;
				break;
			}
		}
	}

	/**
	 * Marks a 1-based line number in the file's tokenizer ignore list to suppress conflicting
	 * indentation errors from Generic.WhiteSpace.ScopeIndent, PEAR.Functions.FunctionDeclaration,
	 * and Squiz.Functions.MultiLineFunctionDeclaration.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param int $line The 1-based line number on which to ignore conflicting indentation sniff codes.
	 */
	public static function ignoreConflictingIndentationSniffsOnLine(File $phpcsFile, int $line): void
	{
		$existingIgnoreList = $phpcsFile->tokenizer->ignoredLines[$line] ?? null;
		$ignoreList = IgnoreList::getNewInstanceFrom($existingIgnoreList instanceof IgnoreList ? $existingIgnoreList : null);
		foreach (self::CONFLICTING_INDENTATION_SNIFF_CODES as $sniffCode) {
			$ignoreList->set($sniffCode, true);
		}

		$phpcsFile->tokenizer->ignoredLines[$line] = $ignoreList;
	}

	/**
	 * Returns the leading whitespace content on the line before the given token pointer.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param int $pointer The token pointer whose line indentation should be retrieved.
	 * @return string The whitespace string preceding the token on the same line.
	 */
	public static function getIndentation(File $phpcsFile, int $pointer): string
	{
		$firstPointerOnLine = TokenHelper::findFirstTokenOnLine($phpcsFile, $pointer);

		return TokenHelper::getContent($phpcsFile, $firstPointerOnLine, $pointer - 1);
	}

	/**
	 * Appends the specified number of indentation levels to the given base indentation string.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param string $indentation The base indentation string.
	 * @param int $level Number of indentation levels to add (defaults to 1).
	 * @return string The resulting indentation string.
	 */
	public static function addIndentation(File $phpcsFile, string $indentation, int $level = 1): string
	{
		return $indentation . str_repeat(self::getOneIndentationLevel($phpcsFile), $level);
	}

	/**
	 * Returns the whitespace string representing a single indentation level for the given file,
	 * using spaces according to configured tabWidth (or DEFAULT_INDENTATION_WIDTH when tabWidth is 0).
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @return string Whitespace string for one indentation level.
	 */
	public static function getOneIndentationLevel(File $phpcsFile): string
	{
		return str_repeat(' ', $phpcsFile->config->tabWidth !== 0 ? $phpcsFile->config->tabWidth : self::DEFAULT_INDENTATION_WIDTH);
	}

	/**
	 * Removes one level of indentation from each line spanned by the given token pointers,
	 * preserving heredoc and nowdoc content unchanged.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param list<int> $codePointers Consecutive token pointers forming the code block.
	 * @param string $defaultIndentation Fallback indentation used when a line does not start with one full indentation level.
	 * @return string The unindented, right-trimmed code string.
	 */
	public static function removeIndentation(File $phpcsFile, array $codePointers, string $defaultIndentation): string
	{
		$tokens = $phpcsFile->getTokens();

		$eolLength = strlen($phpcsFile->eolChar);

		$code = '';
		$inHeredoc = false;

		$indentation = self::getOneIndentationLevel($phpcsFile);
		$indentationLength = strlen($indentation);

		foreach ($codePointers as $no => $codePointer) {
			$content = $tokens[$codePointer]['content'];

			if (
				!$inHeredoc
				&& (
					$no === 0
					|| substr($tokens[$codePointer - 1]['content'], -$eolLength) === $phpcsFile->eolChar
				)
			) {
				if ($content === $phpcsFile->eolChar) {
					// Nothing
				} elseif (substr($content, 0, $indentationLength) === $indentation) {
					$content = substr($content, $indentationLength);
				} else {
					$content = $defaultIndentation . ltrim($content);
				}
			}

			if (in_array($tokens[$codePointer]['code'], [T_START_HEREDOC, T_START_NOWDOC], true)) {
				$inHeredoc = true;
			} elseif (in_array($tokens[$codePointer]['code'], [T_END_HEREDOC, T_END_NOWDOC], true)) {
				$inHeredoc = false;
			}

			$code .= $content;
		}

		return rtrim($code);
	}

	/**
	 * Converts leading space indentation on each line of the given code string into tabs
	 * according to the file's configured tabWidth. Returns the code unchanged when tabWidth is 0.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param string $code The code string whose leading spaces should be converted to tabs.
	 * @return string The code string with leading spaces converted to tabs.
	 */
	public static function convertSpacesToTabs(File $phpcsFile, string $code): string
	{
		// @codeCoverageIgnoreStart
		if ($phpcsFile->config->tabWidth === 0) {
			return $code;
		}
		// @codeCoverageIgnoreEnd

		return preg_replace_callback('~^([ ]+)~m', static function (array $matches) use ($phpcsFile): string {
			$tabsCount = (int) floor(strlen($matches[1]) / $phpcsFile->config->tabWidth);
			$spacesCountToRemove = $tabsCount * $phpcsFile->config->tabWidth;

			return str_repeat("\t", $tabsCount) . substr($matches[1], $spacesCountToRemove);
		}, $code);
	}

	/**
	 * Reformats a trailing operator so that it leads the next line with the given indentation
	 * followed by a single space before its right-hand operand.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param int $pointerBeforeOperator The token pointer to the last non-whitespace token before the operator.
	 * @param int $operatorPointer The token pointer to the operator.
	 * @param int $pointerAfterOperator The token pointer to the first non-whitespace token after the operator.
	 * @param string $expectedIndentation The expected whitespace indentation string before the operator.
	 */
	public static function reformatOperatorAsLeading(
		File $phpcsFile,
		int $pointerBeforeOperator,
		int $operatorPointer,
		int $pointerAfterOperator,
		string $expectedIndentation
	): void
	{
		FixerHelper::removeBetween($phpcsFile, $pointerBeforeOperator, $operatorPointer);
		FixerHelper::removeBetween($phpcsFile, $operatorPointer, $pointerAfterOperator);

		FixerHelper::addBefore($phpcsFile, $operatorPointer, $phpcsFile->eolChar . $expectedIndentation);
		FixerHelper::addBefore($phpcsFile, $pointerAfterOperator, ' ');
	}

	/**
	 * Verifies that an operator at the start of a continuation line has the exact expected indentation
	 * before it and a single space after it, reporting and fixing any violation.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param int $operatorPointer The token pointer to the leading operator.
	 * @param int $pointerAfterOperator The token pointer to the first non-whitespace token after the operator.
	 * @param string $expectedIndentation The expected whitespace indentation string before the operator.
	 * @param string $errorMessage Error message to report when indentation or spacing is invalid.
	 * @param string $errorCode Sniff error code to report.
	 */
	public static function checkLeadingOperatorIndentation(
		File $phpcsFile,
		int $operatorPointer,
		int $pointerAfterOperator,
		string $expectedIndentation,
		string $errorMessage,
		string $errorCode
	): void
	{
		$tokens = $phpcsFile->getTokens();
		$actualIndentation = self::getIndentation($phpcsFile, $operatorPointer);
		$hasSingleSpaceAfter = $pointerAfterOperator === $operatorPointer + 2 && $tokens[$operatorPointer + 1]['content'] === ' ';

		if ($actualIndentation === $expectedIndentation && $hasSingleSpaceAfter) {
			return;
		}

		$fix = $phpcsFile->addFixableError($errorMessage, $operatorPointer, $errorCode);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		self::fixLineIndentation($phpcsFile, $operatorPointer, $expectedIndentation);
		FixerHelper::removeBetween($phpcsFile, $operatorPointer, $pointerAfterOperator);
		FixerHelper::addBefore($phpcsFile, $pointerAfterOperator, ' ');

		$phpcsFile->fixer->endChangeset();
	}

	/**
	 * Replaces the leading whitespace before the given token pointer with the expected indentation.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param int $pointer The token pointer at the start of content on its line.
	 * @param string $expectedIndentation The expected whitespace indentation string.
	 */
	public static function fixLineIndentation(File $phpcsFile, int $pointer, string $expectedIndentation): void
	{
		$firstTokenOnLine = TokenHelper::findFirstTokenOnLine($phpcsFile, $pointer);

		if ($firstTokenOnLine < $pointer) {
			FixerHelper::removeBetweenIncluding($phpcsFile, $firstTokenOnLine, $pointer - 1);
		}

		FixerHelper::addBefore($phpcsFile, $pointer, $expectedIndentation);
	}

	/**
	 * Finds the first effective token pointer of the expression containing the given operator pointer,
	 * skipping backward over balanced parentheses, brackets, and curly braces.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param int $operatorPointer The token pointer to the operator.
	 * @param list<int|string> $additionalStopTokens Additional token codes that terminate the backward scan.
	 * @return int The token pointer to the first effective token of the expression.
	 */
	public static function getExpressionStartPointer(File $phpcsFile, int $operatorPointer, array $additionalStopTokens = []): int
	{
		$tokens = $phpcsFile->getTokens();

		$stopTokens = array_merge(
			[
				T_RETURN,
				T_THROW,
				T_YIELD,
				T_YIELD_FROM,
				T_ECHO,
				T_PRINT,
				T_INCLUDE,
				T_INCLUDE_ONCE,
				T_REQUIRE,
				T_REQUIRE_ONCE,
				T_CASE,
				T_OPEN_TAG,
				T_OPEN_TAG_WITH_ECHO,
				T_DOUBLE_ARROW,
				T_FN_ARROW,
				T_MATCH_ARROW,
				T_COLON,
				T_SEMICOLON,
				T_OPEN_SQUARE_BRACKET,
				T_OPEN_SHORT_ARRAY,
				T_OPEN_PARENTHESIS,
				T_OPEN_CURLY_BRACKET,
				T_CLOSE_PARENTHESIS,
				T_CLOSE_SHORT_ARRAY,
				T_CLOSE_SQUARE_BRACKET,
				T_CLOSE_CURLY_BRACKET,
				T_COMMA,
			],
			array_values(Tokens::ASSIGNMENT_TOKENS),
			$additionalStopTokens,
		);

		$pointerBeforeExpression = $operatorPointer;
		do {
			$pointerBeforeExpression = TokenHelper::findPrevious($phpcsFile, $stopTokens, $pointerBeforeExpression - 1);

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
