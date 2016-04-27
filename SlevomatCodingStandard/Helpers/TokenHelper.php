<?php

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer_File;

class TokenHelper
{

	public static $nameTokenCodes = [
		T_NS_SEPARATOR,
		T_STRING,
	];

	public static $typeKeywordTokenCodes = [
		T_CLASS,
		T_TRAIT,
		T_INTERFACE,
	];

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

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param integer $startPointer search starts at this token, inclusive
	 * @param integer|null $endPointer search ends at this token, exclusive
	 * @return integer|null
	 */
	public static function findNextEffective(PHP_CodeSniffer_File $phpcsFile, $startPointer, $endPointer = null)
	{
		return self::findNextExcluding($phpcsFile, self::$ineffectiveTokenCodes, $startPointer, $endPointer);
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param integer|integer[] $types
	 * @param integer $startPointer search starts at this token, inclusive
	 * @param integer|null $endPointer search ends at this token, exclusive
	 * @return integer|null
	 */
	public static function findNextExcluding(PHP_CodeSniffer_File $phpcsFile, $types, $startPointer, $endPointer = null)
	{
		$token = $phpcsFile->findNext($types, $startPointer, $endPointer, true);
		if ($token === false) {
			return null;
		}
		return $token;
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param integer $startPointer search starts at this token, inclusive
	 * @param integer|null $endPointer search ends at this token, exclusive
	 * @return integer|null
	 */
	public static function findNextAnyToken(PHP_CodeSniffer_File $phpcsFile, $startPointer, $endPointer = null)
	{
		return self::findNextExcluding($phpcsFile, [], $startPointer, $endPointer);
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param integer $startPointer search starts at this token, inclusive
	 * @param integer|null $endPointer search ends at this token, exclusive
	 * @return integer|null
	 */
	public static function findPreviousEffective(PHP_CodeSniffer_File $phpcsFile, $startPointer, $endPointer = null)
	{
		return self::findPreviousExcluding($phpcsFile, self::$ineffectiveTokenCodes, $startPointer, $endPointer);
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param integer[]|integer $types
	 * @param integer $startPointer search starts at this token, inclusive
	 * @param integer|null $endPointer search ends at this token, exclusive
	 * @return integer|null
	 */
	public static function findPreviousExcluding(PHP_CodeSniffer_File $phpcsFile, $types, $startPointer, $endPointer = null)
	{
		$token = $phpcsFile->findPrevious($types, $startPointer, $endPointer, true);
		if ($token === false) {
			return null;
		}
		return $token;
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param integer $pointer search starts at this token, inclusive
	 * @return integer|null
	 */
	public static function findFirstTokenOnNextLine(PHP_CodeSniffer_File $phpcsFile, $pointer)
	{
		$newLinePointer = $phpcsFile->findNext(T_WHITESPACE, $pointer, null, false, $phpcsFile->eolChar);
		if ($newLinePointer === false) {
			return null;
		}
		$tokens = $phpcsFile->getTokens();
		return isset($tokens[$newLinePointer + 1]) ? $newLinePointer + 1 : null;
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param integer $startPointer search starts at this token, inclusive
	 * @param integer|null $endPointer search ends at this token, exclusive
	 * @return string
	 */
	public static function getContent(PHP_CodeSniffer_File $phpcsFile, $startPointer, $endPointer = null)
	{
		$tokens = $phpcsFile->getTokens();
		$content = '';
		while (true) {
			$pointer = self::findNextAnyToken($phpcsFile, $startPointer, $endPointer);
			if ($pointer === null) {
				break;
			}
			$token = $tokens[$pointer];
			$content .= $token['content'];

			$startPointer = $pointer + 1;
		}

		return $content;
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @return integer
	 */
	public static function getLastTokenPointer(PHP_CodeSniffer_File $phpcsFile)
	{
		$tokenCount = count($phpcsFile->getTokens());
		if ($tokenCount === 0) {
			throw new \SlevomatCodingStandard\Helpers\EmptyFileException($phpcsFile->getFilename());
		}
		return $tokenCount - 1;
	}

}
