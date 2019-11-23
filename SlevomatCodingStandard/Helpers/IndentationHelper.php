<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;
use function array_map;
use function explode;
use function implode;
use function ltrim;
use function rtrim;
use function substr;
use const T_WHITESPACE;

/**
 * @internal
 */
class IndentationHelper
{

	private const TAB_INDENT = "\t";
	private const SPACES_INDENT = '    ';

	public static function getIndentation(File $phpcsFile, int $pointer): string
	{
		$endOfLinePointer = TokenHelper::findPreviousContent($phpcsFile, T_WHITESPACE, $phpcsFile->eolChar, $pointer - 1);

		if ($endOfLinePointer === null) {
			return '';
		}

		return TokenHelper::getContent($phpcsFile, $endOfLinePointer + 1, $pointer - 1);
	}

	public static function addIndentation(string $identation): string
	{
		if ($identation === '') {
			return self::TAB_INDENT;
		}

		return $identation . ($identation[0] === self::TAB_INDENT ? self::TAB_INDENT : self::SPACES_INDENT);
	}

	public static function fixIndentation(string $code, string $eolChar, string $defaultIndentation): string
	{
		/** @var string[] $lines */
		$lines = explode($eolChar, rtrim($code));

		return implode($eolChar, array_map(static function (string $line) use ($defaultIndentation): string {
			if ($line === '') {
				return $line;
			}

			if ($line[0] === self::TAB_INDENT) {
				return substr($line, 1);
			}

			if (substr($line, 0, 4) === self::SPACES_INDENT) {
				return substr($line, 4);
			}

			return $defaultIndentation . ltrim($line);
		}, $lines));
	}

}
