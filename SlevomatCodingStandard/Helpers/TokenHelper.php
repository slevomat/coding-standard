<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class TokenHelper
{

	/** @var mixed[] */
	public static $nameTokenCodes = [
		T_NS_SEPARATOR,
		T_STRING,
	];

	/** @var mixed[] */
	public static $typeKeywordTokenCodes = [
		T_CLASS,
		T_TRAIT,
		T_INTERFACE,
	];

	/** @var mixed[] */
	public static $ineffectiveTokenCodes = [
		T_WHITESPACE,
		T_COMMENT,
		T_DOC_COMMENT,
		T_DOC_COMMENT_OPEN_TAG,
		T_DOC_COMMENT_CLOSE_TAG,
		T_DOC_COMMENT_STAR,
		T_DOC_COMMENT_STRING,
		T_DOC_COMMENT_TAG,
		T_DOC_COMMENT_WHITESPACE,
	];

	/** @var mixed[] */
	public static $typeHintTokenCodes = [
		T_NS_SEPARATOR,
		T_STRING,
		T_SELF,
		T_PARENT,
		T_ARRAY_HINT,
		T_CALLABLE,
	];

	/** @var mixed[] */
	public static $earlyExitTokenCodes = [
		T_RETURN,
		T_CONTINUE,
		T_BREAK,
		T_THROW,
		T_YIELD,
		T_YIELD_FROM,
		T_EXIT,
	];

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param mixed|mixed[] $types
	 * @param int $startPointer
	 * @param int|null $endPointer
	 * @return int|null
	 */
	public static function findNext(\PHP_CodeSniffer\Files\File $phpcsFile, $types, int $startPointer, ?int $endPointer = null): ?int
	{
		/** @var int|false $token */
		$token = $phpcsFile->findNext($types, $startPointer, $endPointer, false);
		return $token === false ? null : $token;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param mixed|mixed[] $types
	 * @param int $startPointer
	 * @param int|null $endPointer
	 * @return int[]
	 */
	public static function findNextAll(\PHP_CodeSniffer\Files\File $phpcsFile, $types, int $startPointer, ?int $endPointer = null): array
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
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param mixed|mixed[] $types
	 * @param string $content
	 * @param int $startPointer
	 * @param int|null $endPointer
	 * @return int|null
	 */
	public static function findNextContent(\PHP_CodeSniffer\Files\File $phpcsFile, $types, string $content, int $startPointer, ?int $endPointer = null): ?int
	{
		/** @var int|false $token */
		$token = $phpcsFile->findNext($types, $startPointer, $endPointer, false, $content);
		return $token === false ? null : $token;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $startPointer search starts at this token, inclusive
	 * @param int|null $endPointer search ends at this token, exclusive
	 * @return int|null
	 */
	public static function findNextEffective(\PHP_CodeSniffer\Files\File $phpcsFile, int $startPointer, ?int $endPointer = null): ?int
	{
		return self::findNextExcluding($phpcsFile, self::$ineffectiveTokenCodes, $startPointer, $endPointer);
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param mixed|mixed[] $types
	 * @param int $startPointer search starts at this token, inclusive
	 * @param int|null $endPointer search ends at this token, exclusive
	 * @return int|null
	 */
	public static function findNextExcluding(\PHP_CodeSniffer\Files\File $phpcsFile, $types, int $startPointer, ?int $endPointer = null): ?int
	{
		/** @var int|false $token */
		$token = $phpcsFile->findNext($types, $startPointer, $endPointer, true);
		return $token === false ? null : $token;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param mixed|mixed[] $types
	 * @param int $startPointer
	 * @param int|null $endPointer
	 * @return int|null
	 */
	public static function findNextLocal(\PHP_CodeSniffer\Files\File $phpcsFile, $types, int $startPointer, ?int $endPointer = null): ?int
	{
		/** @var int|false $token */
		$token = $phpcsFile->findNext($types, $startPointer, $endPointer, false, null, true);
		return $token === false ? null : $token;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param mixed|mixed[] $types
	 * @param int $startPointer
	 * @param int|null $endPointer
	 * @return int|null
	 */
	public static function findNextLocalExcluding(\PHP_CodeSniffer\Files\File $phpcsFile, $types, int $startPointer, ?int $endPointer = null): ?int
	{
		/** @var int|false $token */
		$token = $phpcsFile->findNext($types, $startPointer, $endPointer, true, null, true);
		return $token === false ? null : $token;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $startPointer search starts at this token, inclusive
	 * @param int|null $endPointer search ends at this token, exclusive
	 * @return int|null
	 */
	public static function findNextAnyToken(\PHP_CodeSniffer\Files\File $phpcsFile, int $startPointer, ?int $endPointer = null): ?int
	{
		return self::findNextExcluding($phpcsFile, [], $startPointer, $endPointer);
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param mixed|mixed[] $types
	 * @param int $startPointer search starts at this token, inclusive
	 * @param int|null $endPointer search ends at this token, exclusive
	 * @return int|null
	 */
	public static function findPrevious(\PHP_CodeSniffer\Files\File $phpcsFile, $types, int $startPointer, ?int $endPointer = null): ?int
	{
		/** @var int|false $token */
		$token = $phpcsFile->findPrevious($types, $startPointer, $endPointer, false);
		return $token === false ? null : $token;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $startPointer search starts at this token, inclusive
	 * @param int|null $endPointer search ends at this token, exclusive
	 * @return int|null
	 */
	public static function findPreviousEffective(\PHP_CodeSniffer\Files\File $phpcsFile, int $startPointer, ?int $endPointer = null): ?int
	{
		return self::findPreviousExcluding($phpcsFile, self::$ineffectiveTokenCodes, $startPointer, $endPointer);
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param mixed|mixed[] $types
	 * @param int $startPointer search starts at this token, inclusive
	 * @param int|null $endPointer search ends at this token, exclusive
	 * @return int|null
	 */
	public static function findPreviousExcluding(\PHP_CodeSniffer\Files\File $phpcsFile, $types, int $startPointer, ?int $endPointer = null): ?int
	{
		/** @var int|false $token */
		$token = $phpcsFile->findPrevious($types, $startPointer, $endPointer, true);
		return $token === false ? null : $token;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param mixed|mixed[] $types
	 * @param int $startPointer
	 * @param int|null $endPointer
	 * @return int|null
	 */
	public static function findPreviousLocal(\PHP_CodeSniffer\Files\File $phpcsFile, $types, int $startPointer, ?int $endPointer = null): ?int
	{
		/** @var int|false $token */
		$token = $phpcsFile->findPrevious($types, $startPointer, $endPointer, false, null, true);
		return $token === false ? null : $token;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $pointer search starts at this token, inclusive
	 * @return int|null
	 */
	public static function findFirstTokenOnNextLine(\PHP_CodeSniffer\Files\File $phpcsFile, int $pointer): ?int
	{
		$newLinePointer = self::findNextContent($phpcsFile, [T_WHITESPACE, T_DOC_COMMENT_WHITESPACE], $phpcsFile->eolChar, $pointer);
		if ($newLinePointer === null) {
			return null;
		}
		$tokens = $phpcsFile->getTokens();
		return isset($tokens[$newLinePointer + 1]) ? $newLinePointer + 1 : null;
	}

	public static function getContent(\PHP_CodeSniffer\Files\File $phpcsFile, int $startPointer, ?int $endPointer = null): string
	{
		$tokens = $phpcsFile->getTokens();
		$endPointer = $endPointer ?: self::getLastTokenPointer($phpcsFile);

		$content = '';
		for ($i = $startPointer; $i <= $endPointer; $i++) {
			$content .= $tokens[$i]['content'];
		}

		return $content;
	}

	public static function getLastTokenPointer(\PHP_CodeSniffer\Files\File $phpcsFile): int
	{
		$tokenCount = count($phpcsFile->getTokens());
		if ($tokenCount === 0) {
			throw new \SlevomatCodingStandard\Helpers\EmptyFileException($phpcsFile->getFilename());
		}
		return $tokenCount - 1;
	}

}
