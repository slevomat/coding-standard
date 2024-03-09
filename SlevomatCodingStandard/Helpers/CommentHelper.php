<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use function array_key_exists;
use function in_array;
use function preg_match;
use function strpos;
use const T_COMMENT;

/**
 * @internal
 */
class CommentHelper
{

	public static function isLineComment(File $phpcsFile, int $commentPointer): bool
	{
		return (bool) preg_match('~^(?://|#)(.*)~', $phpcsFile->getTokens()[$commentPointer]['content']);
	}

	public static function getCommentEndPointer(File $phpcsFile, int $commentStartPointer): ?int
	{
		$tokens = $phpcsFile->getTokens();

		if (array_key_exists('comment_closer', $tokens[$commentStartPointer])) {
			return $tokens[$commentStartPointer]['comment_closer'];
		}

		if (self::isLineComment($phpcsFile, $commentStartPointer)) {
			return $commentStartPointer;
		}

		if (strpos($tokens[$commentStartPointer]['content'], '/*') !== 0) {
			// Part of block comment
			return null;
		}

		$commentEndPointer = $commentStartPointer;

		for ($i = $commentStartPointer + 1; $i < $phpcsFile->numTokens; $i++) {
			if ($tokens[$i]['code'] === T_COMMENT) {
				$commentEndPointer = $i;
				continue;
			}

			if (in_array($tokens[$i]['code'], Tokens::$phpcsCommentTokens, true)) {
				$commentEndPointer = $i;
				continue;
			}

			break;
		}

		return $commentEndPointer;
	}

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
