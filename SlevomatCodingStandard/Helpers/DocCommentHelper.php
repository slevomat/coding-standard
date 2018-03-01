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

	/**
	 * @param \PHP_CodeSniffer\Files\File $codeSnifferFile
	 * @param int $pointer
	 * @return \SlevomatCodingStandard\Helpers\Comment[]|null
	 */
	public static function getDocCommentDescription(\PHP_CodeSniffer\Files\File $codeSnifferFile, int $pointer): ?array
	{
		$docCommentOpenPointer = self::findDocCommentOpenToken($codeSnifferFile, $pointer);

		if ($docCommentOpenPointer === null) {
			return null;
		}

		$tokens = $codeSnifferFile->getTokens();
		$descriptionStartPointer = TokenHelper::findNextExcluding(
			$codeSnifferFile,
			[T_DOC_COMMENT_WHITESPACE, T_DOC_COMMENT_STAR],
			$docCommentOpenPointer + 1,
			$tokens[$docCommentOpenPointer]['comment_closer']
		);

		if ($descriptionStartPointer === null) {
			return null;
		}

		if ($tokens[$descriptionStartPointer]['code'] !== T_DOC_COMMENT_STRING) {
			return null;
		}

		$tokenAfterDescriptionPointer = TokenHelper::findNext(
			$codeSnifferFile,
			[T_DOC_COMMENT_TAG, T_DOC_COMMENT_CLOSE_TAG],
			$descriptionStartPointer + 1,
			$tokens[$docCommentOpenPointer]['comment_closer'] + 1
		);

		/** @var \SlevomatCodingStandard\Helpers\Comment[] $comments */
		$comments = [];
		for ($i = $descriptionStartPointer; $i < $tokenAfterDescriptionPointer; $i++) {
			if ($tokens[$i]['code'] !== T_DOC_COMMENT_STRING) {
				continue;
			}

			$comments[] = new Comment($i, trim($tokens[$i]['content']));
		}

		return count($comments) > 0 ? $comments : null;
	}

	public static function hasDocCommentDescription(\PHP_CodeSniffer\Files\File $codeSnifferFile, int $pointer): bool
	{
		return self::getDocCommentDescription($codeSnifferFile, $pointer) !== null;
	}

	public static function findDocCommentOpenToken(\PHP_CodeSniffer\Files\File $codeSnifferFile, int $pointer): ?int
	{
		$tokens = $codeSnifferFile->getTokens();

		if ($tokens[$pointer]['code'] === T_DOC_COMMENT_OPEN_TAG) {
			return $pointer;
		}

		$found = TokenHelper::findPreviousExcluding($codeSnifferFile, [T_WHITESPACE, T_COMMENT, T_PUBLIC, T_PROTECTED, T_PRIVATE, T_VAR, T_FINAL, T_STATIC, T_ABSTRACT, T_CONST, T_CLASS, T_INTERFACE, T_TRAIT], $pointer - 1);
		if ($found !== null && $tokens[$found]['code'] === T_DOC_COMMENT_CLOSE_TAG) {
			return $tokens[$found]['comment_opener'];
		}

		return null;
	}

	public static function isInline(\PHP_CodeSniffer\Files\File $codeSnifferFile, int $docCommentOpenPointer): bool
	{
		$tokens = $codeSnifferFile->getTokens();

		$nextPointer = TokenHelper::findNextExcluding($codeSnifferFile, T_WHITESPACE, $tokens[$docCommentOpenPointer]['comment_closer'] + 1);

		if ($nextPointer !== null && in_array($tokens[$nextPointer]['code'], [T_PUBLIC, T_PROTECTED, T_PRIVATE, T_FINAL, T_STATIC, T_ABSTRACT, T_CONST, T_CLASS, T_INTERFACE, T_TRAIT], true)) {
			return false;
		}

		$docCommentContent = self::getDocComment($codeSnifferFile, $docCommentOpenPointer);
		return strpos($docCommentContent, '@var') === 0;
	}

}
