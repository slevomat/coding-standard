<?php

namespace SlevomatCodingStandard\Helpers;

class DocCommentHelper
{

	/**
	 * @param \PHP_CodeSniffer_File $codeSnifferFile
	 * @param integer $pointer
	 * @return boolean
	 */
	public static function hasDocComment(\PHP_CodeSniffer_File $codeSnifferFile, $pointer)
	{
		return self::findDocCommentOpenToken($codeSnifferFile, $pointer) !== null;
	}

	/**
	 * @param \PHP_CodeSniffer_File $codeSnifferFile
	 * @param integer $pointer
	 * @return boolean
	 */
	public static function hasDocCommentDescription(\PHP_CodeSniffer_File $codeSnifferFile, $pointer)
	{
		$docCommentOpenToken = self::findDocCommentOpenToken($codeSnifferFile, $pointer);
		if ($docCommentOpenToken === null) {
			return false;
		}

		$tokens = $codeSnifferFile->getTokens();
		$found = $codeSnifferFile->findNext([T_DOC_COMMENT_WHITESPACE, T_DOC_COMMENT_STAR], $docCommentOpenToken + 1, $tokens[$docCommentOpenToken]['comment_closer'] - 1, true);

		return $found !== false && $tokens[$found]['code'] === T_DOC_COMMENT_STRING;
	}

	/**
	 * @param \PHP_CodeSniffer_File $codeSnifferFile
	 * @param integer $pointer
	 * @return integer|null
	 */
	public static function findDocCommentOpenToken(\PHP_CodeSniffer_File $codeSnifferFile, $pointer)
	{
		$found = TokenHelper::findPreviousExcluding($codeSnifferFile, [T_WHITESPACE, T_COMMENT, T_PUBLIC, T_PROTECTED, T_PRIVATE, T_FINAL, T_STATIC, T_ABSTRACT, T_CONST, T_CLASS, T_INTERFACE, T_TRAIT], $pointer - 1);
		if ($found !== null && $codeSnifferFile->getTokens()[$found]['code'] === T_DOC_COMMENT_CLOSE_TAG) {
			return $codeSnifferFile->getTokens()[$found]['comment_opener'];
		}

		return null;
	}

}
