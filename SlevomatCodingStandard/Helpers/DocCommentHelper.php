<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTextNode;
use PHPStan\PhpDocParser\Parser\ParserException;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use function array_merge;
use function count;
use function in_array;
use function preg_match;
use function sprintf;
use function strtolower;
use function trim;
use const T_ABSTRACT;
use const T_ATTRIBUTE;
use const T_CLASS;
use const T_CLOSE_CURLY_BRACKET;
use const T_CONST;
use const T_DOC_COMMENT_CLOSE_TAG;
use const T_DOC_COMMENT_OPEN_TAG;
use const T_DOC_COMMENT_STAR;
use const T_DOC_COMMENT_STRING;
use const T_DOC_COMMENT_TAG;
use const T_DOC_COMMENT_WHITESPACE;
use const T_ENUM;
use const T_FINAL;
use const T_FUNCTION;
use const T_INTERFACE;
use const T_OPEN_CURLY_BRACKET;
use const T_PRIVATE;
use const T_PROTECTED;
use const T_PUBLIC;
use const T_READONLY;
use const T_SEMICOLON;
use const T_STATIC;
use const T_TRAIT;
use const T_VAR;
use const T_VARIABLE;
use const T_WHITESPACE;

/**
 * @internal
 */
class DocCommentHelper
{

	public static function hasDocComment(File $phpcsFile, int $pointer): bool
	{
		return self::findDocCommentOpenPointer($phpcsFile, $pointer) !== null;
	}

	public static function getDocComment(File $phpcsFile, int $pointer): ?string
	{
		$docCommentOpenToken = self::findDocCommentOpenPointer($phpcsFile, $pointer);
		if ($docCommentOpenToken === null) {
			return null;
		}

		return trim(
			TokenHelper::getContent(
				$phpcsFile,
				$docCommentOpenToken,
				$phpcsFile->getTokens()[$docCommentOpenToken]['comment_closer']
			)
		);
	}

	/**
	 * @return list<Comment>|null
	 */
	public static function getDocCommentDescription(File $phpcsFile, int $pointer): ?array
	{
		$docCommentOpenPointer = self::findDocCommentOpenPointer($phpcsFile, $pointer);

		if ($docCommentOpenPointer === null) {
			return null;
		}

		$tokens = $phpcsFile->getTokens();
		$descriptionStartPointer = TokenHelper::findNextExcluding(
			$phpcsFile,
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
			$phpcsFile,
			[T_DOC_COMMENT_TAG, T_DOC_COMMENT_CLOSE_TAG],
			$descriptionStartPointer + 1,
			$tokens[$docCommentOpenPointer]['comment_closer'] + 1
		);

		/** @var list<Comment> $comments */
		$comments = [];
		for ($i = $descriptionStartPointer; $i < $tokenAfterDescriptionPointer; $i++) {
			if ($tokens[$i]['code'] !== T_DOC_COMMENT_STRING) {
				continue;
			}

			$comments[] = new Comment($i, trim($tokens[$i]['content']));
		}

		return count($comments) > 0 ? $comments : null;
	}

	public static function hasInheritdocAnnotation(File $phpcsFile, int $pointer): bool
	{
		$docCommentOpenPointer = self::findDocCommentOpenPointer($phpcsFile, $pointer);

		if ($docCommentOpenPointer === null) {
			return false;
		}

		$parsedDocComment = self::parseDocComment($phpcsFile, $docCommentOpenPointer);

		if ($parsedDocComment === null) {
			return false;
		}

		foreach ($parsedDocComment->getNode()->children as $child) {
			if ($child instanceof PhpDocTextNode && strtolower($child->text) === '{@inheritdoc}') {
				return true;
			}

			if ($child instanceof PhpDocTagNode && strtolower($child->name) === '@inheritdoc') {
				return true;
			}
		}

		return false;
	}

	public static function hasDocCommentDescription(File $phpcsFile, int $pointer): bool
	{
		return self::getDocCommentDescription($phpcsFile, $pointer) !== null;
	}

	public static function findDocCommentOpenPointer(File $phpcsFile, int $pointer): ?int
	{
		return SniffLocalCache::getAndSetIfNotCached(
			$phpcsFile,
			sprintf('doc-comment-open-pointer-%d', $pointer),
			static function () use ($phpcsFile, $pointer): ?int {
				$tokens = $phpcsFile->getTokens();

				if ($tokens[$pointer]['code'] === T_DOC_COMMENT_OPEN_TAG) {
					return $pointer;
				}

				$found = TokenHelper::findPrevious(
					$phpcsFile,
					[T_DOC_COMMENT_CLOSE_TAG, T_SEMICOLON, T_CLOSE_CURLY_BRACKET, T_OPEN_CURLY_BRACKET],
					$pointer - 1
				);
				if ($found !== null && $tokens[$found]['code'] === T_DOC_COMMENT_CLOSE_TAG) {
					return $tokens[$found]['comment_opener'];
				}

				return null;
			}
		);
	}

	public static function findDocCommentOwnerPointer(File $phpcsFile, int $docCommentOpenPointer): ?int
	{
		$tokens = $phpcsFile->getTokens();

		$docCommentCloserPointer = $tokens[$docCommentOpenPointer]['comment_closer'];

		if (self::isInline($phpcsFile, $docCommentOpenPointer)) {
			return null;
		}

		$docCommentOwnerPointer = null;

		for ($i = $docCommentCloserPointer + 1; $i < count($tokens); $i++) {
			if ($tokens[$i]['code'] === T_ATTRIBUTE) {
				$i = $tokens[$i]['attribute_closer'];
				continue;
			}

			if (in_array(
				$tokens[$i]['code'],
				[T_PUBLIC, T_PROTECTED, T_PRIVATE, T_VAR, T_READONLY, T_FINAL, T_STATIC, T_ABSTRACT, T_WHITESPACE],
				true
			)) {
				continue;
			}

			if (in_array(
				$tokens[$i]['code'],
				array_merge([T_FUNCTION, T_VARIABLE, T_CONST], TokenHelper::$typeKeywordTokenCodes),
				true
			)) {
				$docCommentOwnerPointer = $i;
			}

			break;
		}

		return $docCommentOwnerPointer;
	}

	public static function isInline(File $phpcsFile, int $docCommentOpenPointer): bool
	{
		$tokens = $phpcsFile->getTokens();

		$nextPointer = TokenHelper::findNextNonWhitespace($phpcsFile, $tokens[$docCommentOpenPointer]['comment_closer'] + 1);

		if (
			$nextPointer !== null
			&& in_array(
				$tokens[$nextPointer]['code'],
				[T_PUBLIC, T_PROTECTED, T_PRIVATE, T_READONLY, T_FINAL, T_STATIC, T_ABSTRACT, T_CONST, T_CLASS, T_INTERFACE, T_TRAIT, T_ENUM],
				true
			)
		) {
			return false;
		}

		$parsedDocComment = self::parseDocComment($phpcsFile, $docCommentOpenPointer);

		if ($parsedDocComment === null) {
			return false;
		}

		foreach ($parsedDocComment->getNode()->getTags() as $annotation) {
			if (preg_match('~^@(?:(?:phpstan|psalm)-)?var~i', $annotation->name) === 1) {
				return true;
			}
		}

		return false;
	}

	public static function parseDocComment(File $phpcsFile, int $docCommentOpenPointer): ?ParsedDocComment
	{
		return SniffLocalCache::getAndSetIfNotCached(
			$phpcsFile,
			sprintf('parsed-doc-comment-%d', $docCommentOpenPointer),
			static function () use ($phpcsFile, $docCommentOpenPointer): ?ParsedDocComment {
				$docComment = self::getDocComment($phpcsFile, $docCommentOpenPointer);

				$docCommentTokens = new TokenIterator(PhpDocParserHelper::getLexer()->tokenize($docComment));

				try {
					$parsedDocComment = PhpDocParserHelper::getParser()->parse($docCommentTokens);

					return new ParsedDocComment(
						$docCommentOpenPointer,
						$phpcsFile->getTokens()[$docCommentOpenPointer]['comment_closer'],
						$parsedDocComment,
						$docCommentTokens
					);
				} catch (ParserException $e) {
					return null;
				}
			}
		);
	}

}
