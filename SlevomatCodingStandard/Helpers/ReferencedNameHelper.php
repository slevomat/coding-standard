<?php

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer_File;

/**
 * Following type name occurrences are considered as a referenced name:
 *
 * - extending a class, implementing an interface
 * - typehinting a class or an interface
 * - creating new instance of a class
 * - class whose static method or a property is accessed
 * - thrown and caught exception names
 *
 * Following occurrences are not considered as a referenced name:
 *
 * - namespace name
 * - type name in a use statement
 * - class name in a class definition
 */
class ReferencedNameHelper
{

	/** @var string[][] Cached data for method getAllReferencedNames, cacheKey(string) => pointer(integer) => name(string) */
	private static $allReferencedTypesCache = [];

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param integer $openTagPointer
	 * @param boolean $searchAnnotations
	 * @return \SlevomatCodingStandard\Helpers\ReferencedName[] referenced names
	 */
	public static function getAllReferencedNames(PHP_CodeSniffer_File $phpcsFile, $openTagPointer, $searchAnnotations = false)
	{
		$cacheKey = $phpcsFile->getFilename() . '-' . $openTagPointer . ($searchAnnotations ? '-annotations' : '-no-annotations');
		if (!isset(self::$allReferencedTypesCache[$cacheKey])) {
			self::$allReferencedTypesCache[$cacheKey] = self::createAllReferencedNames($phpcsFile, $openTagPointer, $searchAnnotations);
		}

		return self::$allReferencedTypesCache[$cacheKey];
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param integer $openTagPointer
	 * @param boolean $searchAnnotations
	 * @return \SlevomatCodingStandard\Helpers\ReferencedName[] referenced names
	 */
	private static function createAllReferencedNames(PHP_CodeSniffer_File $phpcsFile, $openTagPointer, $searchAnnotations)
	{
		$beginSearchAtPointer = $openTagPointer + 1;
		$tokens = $phpcsFile->getTokens();
		$phpDocTypes = [
			T_DOC_COMMENT_STRING,
			T_DOC_COMMENT_TAG,
		];

		$searchTypes = TokenHelper::$nameTokenCodes;
		if ($searchAnnotations) {
			$searchTypes = array_merge($phpDocTypes, $searchTypes);
		}

		$types = [];

		$matchTypesInAnnotation = function ($annotation, $nameStartPointer) use (&$types) {
			$annotation = trim($annotation, '@ ');
			if (preg_match('#([a-zA-Z0-9|\[\]\\\]+)#', $annotation, $matches) > 0) {
				$referencedNames = array_filter(array_map(function ($name) {
					return trim($name, '[]');
				}, explode('|', $matches[1])), function ($match) {
					return !in_array($match, [
						'null',
						'self',
						'static',
						'mixed',
						'array',
						'string',
						'int',
						'integer',
						'bool',
						'boolean',
					], true);
				});
				foreach ($referencedNames as $name) {
					$types[] = new ReferencedName($name, $nameStartPointer);
				}
			}
		};

		while (true) {
			$nameStartPointer = $phpcsFile->findNext($searchTypes, $beginSearchAtPointer);
			if ($nameStartPointer === false) {
				break;
			}
			$nameStartToken = $tokens[$nameStartPointer];
			if (in_array($nameStartToken['code'], $phpDocTypes, true)) {
				if ($nameStartToken['code'] === T_DOC_COMMENT_TAG) {
					if (
						!StringHelper::startsWith($nameStartToken['content'], '@var')
						&& !StringHelper::startsWith($nameStartToken['content'], '@param')
						&& !StringHelper::startsWith($nameStartToken['content'], '@return')
						&& !StringHelper::startsWith($nameStartToken['content'], '@throws')
						&& !StringHelper::startsWith($nameStartToken['content'], '@see')
						&& !StringHelper::startsWith($nameStartToken['content'], '@link')
						&& !StringHelper::startsWith($nameStartToken['content'], '@inherit')
					) {
						$matchTypesInAnnotation($nameStartToken['content'], $nameStartPointer);
					}
				} elseif ($nameStartToken['code'] === T_DOC_COMMENT_STRING) {
					$matchTypesInAnnotation($nameStartToken['content'], $nameStartPointer);
				}

				$beginSearchAtPointer = $nameStartPointer + 1;
				continue;
			}
			$nameEndPointer = self::findReferencedNameEndPointer($phpcsFile, $nameStartPointer);
			if ($nameEndPointer === null) {
				$beginSearchAtPointer = TokenHelper::findNextExcluding(
					$phpcsFile,
					array_merge([T_WHITESPACE], TokenHelper::$nameTokenCodes),
					$nameStartPointer
				);
				continue;
			}
			$types[] = new ReferencedName(TokenHelper::getContent($phpcsFile, $nameStartPointer, $nameEndPointer), $nameStartPointer);
			$beginSearchAtPointer = $nameEndPointer + 1;
		}
		return $types;
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param integer $startPointer
	 * @return integer|null
	 */
	public static function findReferencedNameEndPointer(PHP_CodeSniffer_File $phpcsFile, $startPointer)
	{
		if (!self::isReferencedName($phpcsFile, $startPointer)) {
			return null;
		}

		return TokenHelper::findNextExcluding($phpcsFile, TokenHelper::$nameTokenCodes, $startPointer + 1);
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param integer $startPointer
	 * @return boolean
	 */
	private static function isReferencedName(PHP_CodeSniffer_File $phpcsFile, $startPointer)
	{
		$tokens = $phpcsFile->getTokens();

		$previousPointer = TokenHelper::findPreviousNonWhitespace($phpcsFile, $startPointer - 1);
		$previousToken = $tokens[$previousPointer];

		$skipTokenCodes = [
			T_FUNCTION,
			T_USE,
			T_AS,
			T_DOUBLE_COLON,
			T_OBJECT_OPERATOR,
			T_NAMESPACE,
		];

		return !in_array(
			$previousToken['code'],
			array_merge($skipTokenCodes, TokenHelper::$typeKeywordTokenCodes),
			true
		);
	}

}
