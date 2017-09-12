<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class DocCommentHelper
{

	public static function hasDocComment(\PHP_CodeSniffer\Files\File $codeSnifferFile, int $pointer): bool
	{
		return self::findDocCommentOpenToken($codeSnifferFile, $pointer) !== null;
	}

	public static function getDocComment(\PHP_CodeSniffer\Files\File $codeSnifferFile, int $pointer): ?string
	{
		$docCommentOpenToken = self::findDocCommentOpenToken($codeSnifferFile, $pointer);
		if ($docCommentOpenToken === null) {
			return null;
		}

		return trim(TokenHelper::getContent($codeSnifferFile, $docCommentOpenToken + 1, $codeSnifferFile->getTokens()[$docCommentOpenToken]['comment_closer'] - 1));
	}

	public static function hasDocCommentDescription(\PHP_CodeSniffer\Files\File $codeSnifferFile, int $pointer): bool
	{
		$docCommentOpenToken = self::findDocCommentOpenToken($codeSnifferFile, $pointer);
		if ($docCommentOpenToken === null) {
			return false;
		}

		$tokens = $codeSnifferFile->getTokens();
		$found = TokenHelper::findNextExcluding($codeSnifferFile, [T_DOC_COMMENT_WHITESPACE, T_DOC_COMMENT_STAR], $docCommentOpenToken + 1, $tokens[$docCommentOpenToken]['comment_closer'] - 1);

		return $found !== null && $tokens[$found]['code'] === T_DOC_COMMENT_STRING;
	}

	public static function findDocCommentOpenToken(\PHP_CodeSniffer\Files\File $codeSnifferFile, int $pointer): ?int
	{
		$found = TokenHelper::findPreviousExcluding($codeSnifferFile, [T_WHITESPACE, T_COMMENT, T_PUBLIC, T_PROTECTED, T_PRIVATE, T_FINAL, T_STATIC, T_ABSTRACT, T_CONST, T_CLASS, T_INTERFACE, T_TRAIT], $pointer - 1);
		if ($found !== null && $codeSnifferFile->getTokens()[$found]['code'] === T_DOC_COMMENT_CLOSE_TAG) {
			return $codeSnifferFile->getTokens()[$found]['comment_opener'];
		}

		return null;
	}

}
