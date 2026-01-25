<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTextNode;
use PHPStan\PhpDocParser\Parser\ParserException;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use function count;
use function in_array;
use function preg_match;
use function sprintf;
use function stripos;
use function strtolower;
use function trim;
use const T_ATTRIBUTE;
use const T_CLOSE_CURLY_BRACKET;
use const T_CONST;
use const T_DOC_COMMENT_CLOSE_TAG;
use const T_DOC_COMMENT_OPEN_TAG;
use const T_DOC_COMMENT_STAR;
use const T_DOC_COMMENT_STRING;
use const T_DOC_COMMENT_TAG;
use const T_DOC_COMMENT_WHITESPACE;
use const T_FUNCTION;
use const T_OPEN_CURLY_BRACKET;
use const T_SEMICOLON;
use const T_VARIABLE;

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
				$phpcsFile->getTokens()[$docCommentOpenToken]['comment_closer'],
			),
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
			$tokens[$docCommentOpenPointer]['comment_closer'],
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
			$tokens[$docCommentOpenPointer]['comment_closer'] + 1,
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
			if ($child instanceof PhpDocTagNode) {
				if (strtolower($child->name) === '@inheritdoc') {
					return true;
				}

				if (stripos((string) $child->value, '{@inheritdoc}') !== false) {
					return true;
				}
			}

			if ($child instanceof PhpDocTextNode && stripos($child->text, '{@inheritdoc}') !== false) {
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
					$pointer - 1,
				);
				if ($found !== null && $tokens[$found]['code'] === T_DOC_COMMENT_CLOSE_TAG) {
					return $tokens[$found]['comment_opener'];
				}

				return null;
			},
		);
	}

	public static function findDocCommentOwnerPointer(File $phpcsFile, int $docCommentOpenPointer): ?int
	{
		$tokens = $phpcsFile->getTokens();

		if (self::isInline($phpcsFile, $docCommentOpenPointer)) {
			return null;
		}

		$docCommentOwnerPointer = null;

		$i = $docCommentOpenPointer;
		do {
			$pointer = TokenHelper::findNext(
				$phpcsFile,
				[T_ATTRIBUTE, T_DOC_COMMENT_OPEN_TAG, T_FUNCTION, T_VARIABLE, T_CONST, ...TokenHelper::CLASS_TYPE_TOKEN_CODES],
				$i + 1,
			);
			if ($pointer === null) {
				break;
			}

			if ($tokens[$pointer]['code'] === T_DOC_COMMENT_OPEN_TAG) {
				return null;
			}

			if ($tokens[$pointer]['code'] === T_ATTRIBUTE) {
				$i = $tokens[$pointer]['attribute_closer'];
				continue;
			}

			$docCommentOwnerPointer = $pointer;
			break;

		} while (true);

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
				[...TokenHelper::MODIFIERS_TOKEN_CODES, ...TokenHelper::CLASS_TYPE_TOKEN_CODES, T_ATTRIBUTE],
				true,
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
						$docCommentTokens,
					);
				} catch (ParserException $e) {
					return null;
				}
			},
		);
	}

}
