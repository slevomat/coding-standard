<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class AnnotationHelper
{

	/**
	 * @param \PHP_CodeSniffer_File $codeSnifferFile
	 * @param integer $pointer
	 * @param string $annotationName
	 * @return string[]
	 */
	public static function getAnnotationsByName(\PHP_CodeSniffer_File $codeSnifferFile, $pointer, $annotationName)
	{
		$annotations = self::getAnnotations($codeSnifferFile, $pointer);

		if (!array_key_exists($annotationName, $annotations)) {
			return [];
		}

		return $annotations[$annotationName];
	}

	/**
	 * @param \PHP_CodeSniffer_File $codeSnifferFile
	 * @param integer $pointer
	 * @return array
	 */
	public static function getAnnotations(\PHP_CodeSniffer_File $codeSnifferFile, $pointer)
	{
		$annotations = [];

		$docCommentOpenToken = DocCommentHelper::findDocCommentOpenToken($codeSnifferFile, $pointer);
		if ($docCommentOpenToken === null) {
			return $annotations;
		}

		$tokens = $codeSnifferFile->getTokens();
		for ($i = $docCommentOpenToken + 1; $i < $tokens[$docCommentOpenToken]['comment_closer']; $i++) {
			if ($tokens[$i]['code'] === T_DOC_COMMENT_TAG) {
				$annotationContentToken = TokenHelper::findNextExcluding($codeSnifferFile, [T_DOC_COMMENT_WHITESPACE], $i + 1);
				$annotations[$tokens[$i]['content']][] = $tokens[$annotationContentToken]['code'] === T_DOC_COMMENT_STRING ? $tokens[$annotationContentToken]['content'] : null;
			}
		}

		return $annotations;
	}

}
