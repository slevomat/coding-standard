<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class AnnotationHelper
{

	/**
	 * @param \PHP_CodeSniffer\Files\File $codeSnifferFile
	 * @param int $pointer
	 * @param string $annotationName
	 * @return \SlevomatCodingStandard\Helpers\Annotation[]
	 */
	public static function getAnnotationsByName(\PHP_CodeSniffer\Files\File $codeSnifferFile, int $pointer, string $annotationName): array
	{
		$annotations = self::getAnnotations($codeSnifferFile, $pointer);

		if (!array_key_exists($annotationName, $annotations)) {
			return [];
		}

		return $annotations[$annotationName];
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $codeSnifferFile
	 * @param int $pointer
	 * @return \SlevomatCodingStandard\Helpers\Annotation[][]
	 */
	public static function getAnnotations(\PHP_CodeSniffer\Files\File $codeSnifferFile, int $pointer): array
	{
		$annotations = [];

		$docCommentOpenToken = DocCommentHelper::findDocCommentOpenToken($codeSnifferFile, $pointer);
		if ($docCommentOpenToken === null) {
			return $annotations;
		}

		$tokens = $codeSnifferFile->getTokens();
		for ($i = $docCommentOpenToken + 1; $i < $tokens[$docCommentOpenToken]['comment_closer']; $i++) {
			if ($tokens[$i]['code'] === T_DOC_COMMENT_TAG) {

				// Fix for wrong PHPCS parsing
				$annotationCode = $tokens[$i]['content'];
				for ($j = $i + 1; $j < $tokens[$docCommentOpenToken]['comment_closer']; $j++) {
					if (!in_array($tokens[$j]['code'], [T_DOC_COMMENT_WHITESPACE, T_DOC_COMMENT_STRING], true)) {
						break;
					}
					$annotationCode .= $tokens[$j]['content'];
				}

				$annotationName = $tokens[$i]['content'];
				$annotationParameters = null;
				$annotationContent = null;
				if (preg_match('~^(@[a-zA-Z\\\\]+)(?:\(([^)]*)\))?(?:\\s+(.+))?$~s', $annotationCode, $matches)) {
					$annotationName = $matches[1];
					$annotationParameters = $matches[2] ?: null;
					if (isset($matches[3])) {
						$annotationContent = trim($matches[3]);
						if ($annotationContent === '') {
							$annotationContent = null;
						}
					}
				}

				$annotations[$annotationName][] = new Annotation($annotationName, $annotationParameters, $annotationContent);
			}
		}

		return $annotations;
	}

}
