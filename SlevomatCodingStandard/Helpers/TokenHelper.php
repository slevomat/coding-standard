<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;
use function array_key_exists;
use function count;
use const T_ABSTRACT;
use const T_ANON_CLASS;
use const T_ARRAY;
use const T_BREAK;
use const T_CALLABLE;
use const T_CLASS;
use const T_CLOSURE;
use const T_COMMENT;
use const T_CONTINUE;
use const T_DOC_COMMENT;
use const T_DOC_COMMENT_CLOSE_TAG;
use const T_DOC_COMMENT_OPEN_TAG;
use const T_DOC_COMMENT_STAR;
use const T_DOC_COMMENT_STRING;
use const T_DOC_COMMENT_TAG;
use const T_DOC_COMMENT_WHITESPACE;
use const T_ENUM;
use const T_EXIT;
use const T_FALSE;
use const T_FINAL;
use const T_FN;
use const T_FUNCTION;
use const T_INTERFACE;
use const T_NAME_FULLY_QUALIFIED;
use const T_NAME_QUALIFIED;
use const T_NAME_RELATIVE;
use const T_NULL;
use const T_OPEN_SHORT_ARRAY;
use const T_PARENT;
use const T_PHPCS_DISABLE;
use const T_PHPCS_ENABLE;
use const T_PHPCS_IGNORE;
use const T_PHPCS_IGNORE_FILE;
use const T_PHPCS_SET;
use const T_PRIVATE;
use const T_PRIVATE_SET;
use const T_PROTECTED;
use const T_PROTECTED_SET;
use const T_PUBLIC;
use const T_PUBLIC_SET;
use const T_READONLY;
use const T_RETURN;
use const T_SELF;
use const T_STATIC;
use const T_STRING;
use const T_THROW;
use const T_TRAIT;
use const T_TRUE;
use const T_TYPE_CLOSE_PARENTHESIS;
use const T_TYPE_INTERSECTION;
use const T_TYPE_OPEN_PARENTHESIS;
use const T_TYPE_UNION;
use const T_VAR;
use const T_WHITESPACE;

/**
 * @internal
 */
class TokenHelper
{

	public const NAME_TOKEN_CODES = [
		T_STRING,
		T_NAME_FULLY_QUALIFIED,
		T_NAME_QUALIFIED,
		T_NAME_RELATIVE,
	];

	public const CLASS_KEYWORD_CODES = [
		T_PARENT,
		T_SELF,
		T_STATIC,
	];

	public const ONLY_TYPE_HINT_TOKEN_CODES = [
		...self::NAME_TOKEN_CODES,
		T_SELF,
		T_PARENT,
		T_CALLABLE,
		T_FALSE,
		T_TRUE,
		T_NULL,
	];

	public const TYPE_HINT_TOKEN_CODES = [
		...self::ONLY_TYPE_HINT_TOKEN_CODES,
		T_TYPE_UNION,
		T_TYPE_INTERSECTION,
		T_TYPE_OPEN_PARENTHESIS,
		T_TYPE_CLOSE_PARENTHESIS,
	];

	public const MODIFIERS_TOKEN_CODES = [
		T_FINAL,
		T_ABSTRACT,
		T_VAR,
		T_PUBLIC,
		T_PUBLIC_SET,
		T_PROTECTED,
		T_PROTECTED_SET,
		T_PRIVATE,
		T_PRIVATE_SET,
		T_READONLY,
		T_STATIC,
	];

	public const PROPERTY_MODIFIERS_TOKEN_CODES = self::MODIFIERS_TOKEN_CODES;

	public const ARRAY_TOKEN_CODES = [
		T_ARRAY,
		T_OPEN_SHORT_ARRAY,
	];

	public const CLASS_TYPE_TOKEN_CODES = [
		T_CLASS,
		T_TRAIT,
		T_INTERFACE,
		T_ENUM,
	];

	public const CLASS_TYPE_WITH_ANONYMOUS_CLASS_TOKEN_CODES = [
		...self::CLASS_TYPE_TOKEN_CODES,
		T_ANON_CLASS,
	];

	public const ANNOTATION_TOKEN_CODES = [
		T_DOC_COMMENT_TAG,
		T_PHPCS_DISABLE,
		T_PHPCS_ENABLE,
		T_PHPCS_IGNORE,
		T_PHPCS_IGNORE_FILE,
		T_PHPCS_SET,
	];

	public const INLINE_COMMENT_TOKEN_CODES = [
		T_COMMENT,
		T_PHPCS_DISABLE,
		T_PHPCS_ENABLE,
		T_PHPCS_IGNORE,
		T_PHPCS_IGNORE_FILE,
		T_PHPCS_SET,
	];

	public const INEFFECTIVE_TOKEN_CODES = [
		T_WHITESPACE,
		T_DOC_COMMENT,
		T_DOC_COMMENT_OPEN_TAG,
		T_DOC_COMMENT_CLOSE_TAG,
		T_DOC_COMMENT_STAR,
		T_DOC_COMMENT_STRING,
		T_DOC_COMMENT_TAG,
		T_DOC_COMMENT_WHITESPACE,
		...self::INLINE_COMMENT_TOKEN_CODES,
	];

	public const EARLY_EXIT_TOKEN_CODES = [
		T_RETURN,
		T_CONTINUE,
		T_BREAK,
		T_THROW,
		T_EXIT,
	];

	public const FUNCTION_TOKEN_CODES = [
		T_FUNCTION,
		T_CLOSURE,
		T_FN,
	];

	/**
	 * @param int|string|array<int|string, int|string> $types
	 */
	public static function findNext(File $phpcsFile, $types, int $startPointer, ?int $endPointer = null): ?int
	{
		/** @var int|false $token */
		$token = $phpcsFile->findNext($types, $startPointer, $endPointer, false);
		return $token === false ? null : $token;
	}

	/**
	 * @param int|string|array<int|string, int|string> $types
	 * @return list<int>
	 */
	public static function findNextAll(File $phpcsFile, $types, int $startPointer, ?int $endPointer = null): array
	{
		$pointers = [];

		$actualStartPointer = $startPointer;
		while (true) {
			$pointer = self::findNext($phpcsFile, $types, $actualStartPointer, $endPointer);
			if ($pointer === null) {
				break;
			}

			$pointers[] = $pointer;
			$actualStartPointer = $pointer + 1;
		}

		return $pointers;
	}

	/**
	 * @param int|string|array<int|string, int|string> $types
	 */
	public static function findNextContent(File $phpcsFile, $types, string $content, int $startPointer, ?int $endPointer = null): ?int
	{
		/** @var int|false $token */
		$token = $phpcsFile->findNext($types, $startPointer, $endPointer, false, $content);
		return $token === false ? null : $token;
	}

	/**
	 * @param int $startPointer Search starts at this token, inclusive
	 * @param int|null $endPointer Search ends at this token, exclusive
	 */
	public static function findNextEffective(File $phpcsFile, int $startPointer, ?int $endPointer = null): ?int
	{
		return self::findNextExcluding($phpcsFile, self::INEFFECTIVE_TOKEN_CODES, $startPointer, $endPointer);
	}

	/**
	 * @param int $startPointer Search starts at this token, inclusive
	 * @param int|null $endPointer Search ends at this token, exclusive
	 */
	public static function findNextNonWhitespace(File $phpcsFile, int $startPointer, ?int $endPointer = null): ?int
	{
		return self::findNextExcluding($phpcsFile, T_WHITESPACE, $startPointer, $endPointer);
	}

	/**
	 * @param int|string|array<int|string, int|string> $types
	 * @param int $startPointer Search starts at this token, inclusive
	 * @param int|null $endPointer Search ends at this token, exclusive
	 */
	public static function findNextExcluding(File $phpcsFile, $types, int $startPointer, ?int $endPointer = null): ?int
	{
		/** @var int|false $token */
		$token = $phpcsFile->findNext($types, $startPointer, $endPointer, true);
		return $token === false ? null : $token;
	}

	/**
	 * @param int|string|array<int|string, int|string> $types
	 */
	public static function findNextLocal(File $phpcsFile, $types, int $startPointer, ?int $endPointer = null): ?int
	{
		/** @var int|false $token */
		$token = $phpcsFile->findNext($types, $startPointer, $endPointer, false, null, true);
		return $token === false ? null : $token;
	}

	/**
	 * @param int $startPointer Search starts at this token, inclusive
	 * @param int|null $endPointer Search ends at this token, exclusive
	 */
	public static function findNextAnyToken(File $phpcsFile, int $startPointer, ?int $endPointer = null): ?int
	{
		return self::findNextExcluding($phpcsFile, [], $startPointer, $endPointer);
	}

	/**
	 * @param int|string|array<int|string, int|string> $types
	 * @param int $startPointer Search starts at this token, inclusive
	 * @param int|null $endPointer Search ends at this token, exclusive
	 */
	public static function findPrevious(File $phpcsFile, $types, int $startPointer, ?int $endPointer = null): ?int
	{
		/** @var int|false $token */
		$token = $phpcsFile->findPrevious($types, $startPointer, $endPointer, false);
		return $token === false ? null : $token;
	}

	/**
	 * @param int|string|array<int|string, int|string> $types
	 */
	public static function findPreviousContent(File $phpcsFile, $types, string $content, int $startPointer, ?int $endPointer = null): ?int
	{
		/** @var int|false $token */
		$token = $phpcsFile->findPrevious($types, $startPointer, $endPointer, false, $content);
		return $token === false ? null : $token;
	}

	/**
	 * @param int $startPointer Search starts at this token, inclusive
	 * @param int|null $endPointer Search ends at this token, exclusive
	 */
	public static function findPreviousEffective(File $phpcsFile, int $startPointer, ?int $endPointer = null): ?int
	{
		return self::findPreviousExcluding($phpcsFile, self::INEFFECTIVE_TOKEN_CODES, $startPointer, $endPointer);
	}

	/**
	 * @param int $startPointer Search starts at this token, inclusive
	 * @param int|null $endPointer Search ends at this token, exclusive
	 */
	public static function findPreviousNonWhitespace(File $phpcsFile, int $startPointer, ?int $endPointer = null): ?int
	{
		return self::findPreviousExcluding($phpcsFile, T_WHITESPACE, $startPointer, $endPointer);
	}

	/**
	 * @param int|string|array<int|string, int|string> $types
	 * @param int $startPointer Search starts at this token, inclusive
	 * @param int|null $endPointer Search ends at this token, exclusive
	 */
	public static function findPreviousExcluding(File $phpcsFile, $types, int $startPointer, ?int $endPointer = null): ?int
	{
		/** @var int|false $token */
		$token = $phpcsFile->findPrevious($types, $startPointer, $endPointer, true);
		return $token === false ? null : $token;
	}

	/**
	 * @param int|string|array<int|string, int|string> $types
	 */
	public static function findPreviousLocal(File $phpcsFile, $types, int $startPointer, ?int $endPointer = null): ?int
	{
		/** @var int|false $token */
		$token = $phpcsFile->findPrevious($types, $startPointer, $endPointer, false, null, true);
		return $token === false ? null : $token;
	}

	/**
	 * @param int $pointer Search starts at this token, inclusive
	 */
	public static function findFirstTokenOnLine(File $phpcsFile, int $pointer): int
	{
		if ($pointer === 0) {
			return $pointer;
		}

		$tokens = $phpcsFile->getTokens();

		$line = $tokens[$pointer]['line'];

		do {
			$pointer--;
		} while ($tokens[$pointer]['line'] === $line);

		return $pointer + 1;
	}

	/**
	 * @param int $pointer Search starts at this token, inclusive
	 */
	public static function findLastTokenOnLine(File $phpcsFile, int $pointer): int
	{
		$tokens = $phpcsFile->getTokens();

		$line = $tokens[$pointer]['line'];

		do {
			$pointer++;
		} while (array_key_exists($pointer, $tokens) && $tokens[$pointer]['line'] === $line);

		return $pointer - 1;
	}

	/**
	 * @param int $pointer Search starts at this token, inclusive
	 */
	public static function findLastTokenOnPreviousLine(File $phpcsFile, int $pointer): int
	{
		$tokens = $phpcsFile->getTokens();

		$line = $tokens[$pointer]['line'];

		do {
			$pointer--;
		} while ($tokens[$pointer]['line'] === $line);

		return $pointer;
	}

	/**
	 * @param int $pointer Search starts at this token, inclusive
	 */
	public static function findFirstTokenOnNextLine(File $phpcsFile, int $pointer): ?int
	{
		$tokens = $phpcsFile->getTokens();
		if ($pointer >= count($tokens)) {
			return null;
		}

		$line = $tokens[$pointer]['line'];

		do {
			$pointer++;
			if (!array_key_exists($pointer, $tokens)) {
				return null;
			}
		} while ($tokens[$pointer]['line'] === $line);

		return $pointer;
	}

	/**
	 * @param int $pointer Search starts at this token, inclusive
	 */
	public static function findFirstNonWhitespaceOnLine(File $phpcsFile, int $pointer): int
	{
		if ($pointer === 0) {
			return $pointer;
		}

		$tokens = $phpcsFile->getTokens();

		$line = $tokens[$pointer]['line'];

		do {
			$pointer--;
		} while ($pointer >= 0 && $tokens[$pointer]['line'] === $line);

		return self::findNextExcluding($phpcsFile, [T_WHITESPACE, T_DOC_COMMENT_WHITESPACE], $pointer + 1);
	}

	/**
	 * @param int $pointer Search starts at this token, inclusive
	 */
	public static function findFirstNonWhitespaceOnNextLine(File $phpcsFile, int $pointer): ?int
	{
		$newLinePointer = self::findNextContent($phpcsFile, [T_WHITESPACE, T_DOC_COMMENT_WHITESPACE], $phpcsFile->eolChar, $pointer);
		if ($newLinePointer === null) {
			return null;
		}

		$nextPointer = self::findNextExcluding($phpcsFile, [T_WHITESPACE, T_DOC_COMMENT_WHITESPACE], $newLinePointer + 1);

		$tokens = $phpcsFile->getTokens();
		if ($nextPointer !== null && $tokens[$pointer]['line'] === $tokens[$nextPointer]['line'] - 1) {
			return $nextPointer;
		}

		return null;
	}

	/**
	 * @param int $pointer Search starts at this token, inclusive
	 */
	public static function findFirstNonWhitespaceOnPreviousLine(File $phpcsFile, int $pointer): ?int
	{
		$newLinePointerOnPreviousLine = self::findPreviousContent(
			$phpcsFile,
			[T_WHITESPACE, T_DOC_COMMENT_WHITESPACE],
			$phpcsFile->eolChar,
			$pointer,
		);
		if ($newLinePointerOnPreviousLine === null) {
			return null;
		}

		$newLinePointerBeforePreviousLine = self::findPreviousContent(
			$phpcsFile,
			[T_WHITESPACE, T_DOC_COMMENT_WHITESPACE],
			$phpcsFile->eolChar,
			$newLinePointerOnPreviousLine - 1,
		);

		$nextPointer = self::findNextExcluding($phpcsFile, [T_WHITESPACE, T_DOC_COMMENT_WHITESPACE], $newLinePointerBeforePreviousLine + 1);

		$tokens = $phpcsFile->getTokens();
		if ($nextPointer !== null && $tokens[$pointer]['line'] === $tokens[$nextPointer]['line'] + 1) {
			return $nextPointer;
		}

		return null;
	}

	public static function getContent(File $phpcsFile, int $startPointer, ?int $endPointer = null): string
	{
		$tokens = $phpcsFile->getTokens();
		$endPointer ??= self::getLastTokenPointer($phpcsFile);

		$content = '';
		for ($i = $startPointer; $i <= $endPointer; $i++) {
			$content .= $tokens[$i]['content'];
		}

		return $content;
	}

	public static function getLastTokenPointer(File $phpcsFile): int
	{
		$tokenCount = count($phpcsFile->getTokens());
		if ($tokenCount === 0) {
			throw new EmptyFileException($phpcsFile->getFilename());
		}
		return $tokenCount - 1;
	}

}
