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
