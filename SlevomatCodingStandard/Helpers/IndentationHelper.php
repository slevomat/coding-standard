<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;
use function floor;
use function in_array;
use function ltrim;
use function preg_replace_callback;
use function rtrim;
use function str_repeat;
use function strlen;
use function substr;
use const T_END_HEREDOC;
use const T_END_NOWDOC;
use const T_START_HEREDOC;
use const T_START_NOWDOC;

/**
 * @internal
 */
class IndentationHelper
{

	public const DEFAULT_INDENTATION_WIDTH = 4;

	public static function getIndentation(File $phpcsFile, int $pointer): string
	{
		$firstPointerOnLine = TokenHelper::findFirstTokenOnLine($phpcsFile, $pointer);

		return TokenHelper::getContent($phpcsFile, $firstPointerOnLine, $pointer - 1);
	}

	public static function addIndentation(File $phpcsFile, string $indentation, int $level = 1): string
	{
		return $indentation . str_repeat(self::getOneIndentationLevel($phpcsFile), $level);
	}

	public static function getOneIndentationLevel(File $phpcsFile): string
	{
		return str_repeat(' ', $phpcsFile->config->tabWidth !== 0 ? $phpcsFile->config->tabWidth : self::DEFAULT_INDENTATION_WIDTH);
	}

	/**
	 * @param list<int> $codePointers
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
