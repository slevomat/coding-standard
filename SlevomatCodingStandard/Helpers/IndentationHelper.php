<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\ScopeIndentSniff;
use PHP_CodeSniffer\Util\IgnoreList;
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
use const T_END_HEREDOC;
use const T_END_NOWDOC;
use const T_OPEN_TAG;
use const T_START_HEREDOC;
use const T_START_NOWDOC;

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
	 * Registers the given operator token codes in Generic.WhiteSpace.ScopeIndent's
	 * $ignoreIndentationTokens property when that sniff is active in the ruleset.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param list<int|string> $tokenCodes List of operator token codes to ignore in ScopeIndentSniff.
	 */
	public static function ignoreTokensInScopeIndent(File $phpcsFile, array $tokenCodes): void
	{
		$scopeIndentSniff = $phpcsFile->ruleset->sniffs[ScopeIndentSniff::class] ?? null;
		if (!$scopeIndentSniff instanceof ScopeIndentSniff) {
			return;
		}

		foreach ($tokenCodes as $tokenCode) {
			/** @var int $tokenCode */
			if (!in_array($tokenCode, $scopeIndentSniff->ignoreIndentationTokens, true)) {
				$scopeIndentSniff->ignoreIndentationTokens[] = $tokenCode;
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

}
