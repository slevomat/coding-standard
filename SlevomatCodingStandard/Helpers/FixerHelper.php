<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;
use function count;
use function preg_match;

/**
 * @internal
 */
class FixerHelper
{

	public static function change(File $phpcsFile, int $startPointer, int $endPointer, string $content): void
	{
		self::removeBetweenIncluding($phpcsFile, $startPointer, $endPointer);
		$phpcsFile->fixer->replaceToken($startPointer, $content);
	}

	public static function removeBetween(File $phpcsFile, int $startPointer, int $endPointer): void
	{
		self::removeBetweenIncluding($phpcsFile, $startPointer + 1, $endPointer - 1);
	}

	public static function removeBetweenIncluding(File $phpcsFile, int $startPointer, int $endPointer): void
	{
		for ($i = $startPointer; $i <= $endPointer; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}
	}

	public static function removeWhitespaceBefore(File $phpcsFile, int $pointer): void
	{
		for ($i = $pointer - 1; $i > 0; $i--) {
			if (preg_match('~^\\s+$~', $phpcsFile->fixer->getTokenContent($i)) === 0) {
				break;
			}

			$phpcsFile->fixer->replaceToken($i, '');
		}
	}

	public static function removeWhitespaceAfter(File $phpcsFile, int $pointer): void
	{
		for ($i = $pointer + 1; $i < count($phpcsFile->getTokens()); $i++) {
			if (preg_match('~^\\s+$~', $phpcsFile->fixer->getTokenContent($i)) === 0) {
				break;
			}

			$phpcsFile->fixer->replaceToken($i, '');
		}
	}

}
