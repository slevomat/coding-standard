<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;
use function in_array;
use function ltrim;
use function rtrim;
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

	private const TAB_INDENT = "\t";
	private const SPACES_INDENT = '    ';

	public static function getIndentation(File $phpcsFile, int $pointer): string
	{
		$firstPointerOnLine = TokenHelper::findFirstTokenOnLine($phpcsFile, $pointer);

		return TokenHelper::getContent($phpcsFile, $firstPointerOnLine, $pointer - 1);
	}

	public static function addIndentation(string $identation): string
	{
		if ($identation === '') {
			return self::TAB_INDENT;
		}

		return $identation . ($identation[0] === self::TAB_INDENT ? self::TAB_INDENT : self::SPACES_INDENT);
	}

	/**
	 * @param File $phpcsFile
	 * @param int[] $codePointers
	 * @param string $defaultIndentation
	 * @return string
	 */
	public static function fixIndentation(File $phpcsFile, array $codePointers, string $defaultIndentation): string
	{
		$tokens = $phpcsFile->getTokens();

		$eolLength = strlen($phpcsFile->eolChar);

		$code = '';
		$inHeredoc = false;

		foreach ($codePointers as $no => $codePointer) {
			$content = $tokens[$codePointer]['content'];

			if (
				!$inHeredoc
				&& ($no === 0 || substr($tokens[$codePointer - 1]['content'], -$eolLength) === $phpcsFile->eolChar)
			) {
				if ($content === $phpcsFile->eolChar) {
					// Nothing
				} elseif ($content[0] === self::TAB_INDENT) {
					$content = substr($content, 1);
				} elseif (substr($content, 0, 4) === self::SPACES_INDENT) {
					$content = substr($content, 4);
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

}
