<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;

/**
 * @internal
 */
class CommentHelper
{

	public static function getMultilineCommentStartPointer(File $phpcsFile, int $commentEndPointer): int
	{
		$tokens = $phpcsFile->getTokens();

		$commentStartPointer = $commentEndPointer;
		do {
			$commentBefore = TokenHelper::findPrevious($phpcsFile, TokenHelper::$inlineCommentTokenCodes, $commentStartPointer - 1);
			if ($commentBefore === null) {
				break;
			}
			if ($tokens[$commentBefore]['line'] + 1 !== $tokens[$commentStartPointer]['line']) {
				break;
			}

			/** @var int $commentStartPointer */
			$commentStartPointer = $commentBefore;
		} while (true);

		return $commentStartPointer;
	}

	public static function getMultilineCommentEndPointer(File $phpcsFile, int $commentStartPointer): int
	{
		$tokens = $phpcsFile->getTokens();

		$commentEndPointer = $commentStartPointer;
		do {
			$commentAfter = TokenHelper::findNext($phpcsFile, TokenHelper::$inlineCommentTokenCodes, $commentEndPointer + 1);
			if ($commentAfter === null) {
				break;
			}
			if ($tokens[$commentAfter]['line'] - 1 !== $tokens[$commentEndPointer]['line']) {
				break;
			}

			/** @var int $commentEndPointer */
			$commentEndPointer = $commentAfter;
		} while (true);

		return $commentEndPointer;
	}

}
