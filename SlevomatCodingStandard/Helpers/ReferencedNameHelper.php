<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

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

	/** @var \SlevomatCodingStandard\Helpers\ReferencedName[][] Cached data for method getAllReferencedNames, cacheKey(string) => pointer(integer) => name(string) */
	private static $allReferencedTypesCache = [];

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $openTagPointer
	 * @return \SlevomatCodingStandard\Helpers\ReferencedName[] referenced names
	 */
	public static function getAllReferencedNames(\PHP_CodeSniffer_File $phpcsFile, int $openTagPointer): array
	{
		$cacheKey = $phpcsFile->getFilename() . '-' . $openTagPointer;
		if (!isset(self::$allReferencedTypesCache[$cacheKey])) {
			self::$allReferencedTypesCache[$cacheKey] = self::createAllReferencedNames($phpcsFile, $openTagPointer);
		}

		return self::$allReferencedTypesCache[$cacheKey];
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $openTagPointer
	 * @return \SlevomatCodingStandard\Helpers\ReferencedName[] referenced names
	 */
	private static function createAllReferencedNames(\PHP_CodeSniffer_File $phpcsFile, int $openTagPointer): array
	{
		$beginSearchAtPointer = $openTagPointer + 1;
		$tokens = $phpcsFile->getTokens();

		$searchTypes = array_merge([T_RETURN_TYPE], TokenHelper::$nameTokenCodes);
		$types = [];

		while (true) {
			$nameStartPointer = $phpcsFile->findNext($searchTypes, $beginSearchAtPointer);
			if ($nameStartPointer === false) {
				break;
			}
			$nameEndPointer = self::findReferencedNameEndPointer($phpcsFile, $nameStartPointer);
			if ($nameEndPointer === null) {
				$beginSearchAtPointer = TokenHelper::findNextExcluding(
					$phpcsFile,
					array_merge([T_WHITESPACE, T_RETURN_TYPE], TokenHelper::$nameTokenCodes),
					$nameStartPointer
				);
				continue;
			}

			$nextTokenAfterEndPointer = TokenHelper::findNextEffective($phpcsFile, $nameEndPointer);
			$previousTokenBeforeStartPointer = TokenHelper::findPreviousEffective($phpcsFile, $nameStartPointer - 1);
			$type = ReferencedName::TYPE_DEFAULT;
			if ($nextTokenAfterEndPointer !== null && $previousTokenBeforeStartPointer !== null) {
				if ($tokens[$nextTokenAfterEndPointer]['code'] === T_OPEN_PARENTHESIS) {
					if ($tokens[$previousTokenBeforeStartPointer]['code'] !== T_NEW) {
						$type = ReferencedName::TYPE_FUNCTION;
					}
				} elseif ($tokens[$nextTokenAfterEndPointer]['code'] !== T_VARIABLE) {
					if (
						!in_array($tokens[$previousTokenBeforeStartPointer]['code'], [
							T_EXTENDS,
							T_IMPLEMENTS,
							T_INSTANCEOF,
							T_USE, // trait
							T_NEW,
							T_COLON, // return typehint
						], true)
						&& !in_array($tokens[$nextTokenAfterEndPointer]['code'], [
							T_DOUBLE_COLON,
						], true)
					) {
						if ($tokens[$previousTokenBeforeStartPointer]['code'] === T_COMMA) {
							$precedingTokenPointer = TokenHelper::findPreviousExcluding(
								$phpcsFile,
								array_merge([T_COMMA], TokenHelper::$nameTokenCodes, TokenHelper::$ineffectiveTokenCodes),
								$previousTokenBeforeStartPointer - 1
							);
							if (
								!in_array($tokens[$precedingTokenPointer]['code'], [
									T_IMPLEMENTS,
									T_EXTENDS,
									T_USE,
								])
							) {
								$type = ReferencedName::TYPE_CONSTANT;
							}
						} elseif (
							PHP_VERSION_ID >= 70100
							&& ($tokens[$previousTokenBeforeStartPointer]['code'] === T_BITWISE_OR
							|| $tokens[$previousTokenBeforeStartPointer]['code'] === T_OPEN_PARENTHESIS)
						) {
							$exclude = [T_BITWISE_OR, T_OPEN_PARENTHESIS];
							$catchPointer = TokenHelper::findPreviousExcluding(
								$phpcsFile,
								array_merge($exclude, TokenHelper::$nameTokenCodes, TokenHelper::$ineffectiveTokenCodes),
								$previousTokenBeforeStartPointer - 1
							);
							$exclude = [T_BITWISE_OR];
							$openParenthesisPointer = TokenHelper::findPreviousExcluding(
								$phpcsFile,
								array_merge($exclude, TokenHelper::$nameTokenCodes, TokenHelper::$ineffectiveTokenCodes),
								$previousTokenBeforeStartPointer
							);
							if (
								$tokens[$catchPointer]['code'] !== T_CATCH
								|| $tokens[$openParenthesisPointer]['code'] !== T_OPEN_PARENTHESIS
							) {
								$type = ReferencedName::TYPE_CONSTANT;
							}
						} else {
							$type = ReferencedName::TYPE_CONSTANT;
						}
					}
				}
			}
			$types[] = new ReferencedName(TokenHelper::getContent($phpcsFile, $nameStartPointer, $nameEndPointer), $nameStartPointer, $type);
			$beginSearchAtPointer = $nameEndPointer + 1;
		}
		return $types;
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $startPointer
	 * @return int|null
	 */
	public static function findReferencedNameEndPointer(\PHP_CodeSniffer_File $phpcsFile, int $startPointer)
	{
		if (!self::isReferencedName($phpcsFile, $startPointer)) {
			return null;
		}

		return TokenHelper::findNextExcluding($phpcsFile, array_merge([T_RETURN_TYPE], TokenHelper::$nameTokenCodes), $startPointer + 1);
	}

	private static function isReferencedName(\PHP_CodeSniffer_File $phpcsFile, int $startPointer): bool
	{
		$tokens = $phpcsFile->getTokens();

		$previousPointer = TokenHelper::findPreviousEffective($phpcsFile, $startPointer - 1);
		$previousToken = $tokens[$previousPointer];

		$skipTokenCodes = [
			T_FUNCTION,
			T_AS,
			T_DOUBLE_COLON,
			T_OBJECT_OPERATOR,
			T_NAMESPACE,
			T_CONST,
		];

		if ($previousToken['code'] === T_USE) {
			$classPointer = $phpcsFile->findPrevious(T_CLASS, $startPointer - 1);
			if ($classPointer !== false) {
				$tokens = $phpcsFile->getTokens();
				$classToken = $tokens[$classPointer];
				return $startPointer > $classToken['scope_opener'] && $startPointer < $classToken['scope_closer'];
			}

			return false;
		}

		return !in_array(
			$previousToken['code'],
			array_merge($skipTokenCodes, TokenHelper::$typeKeywordTokenCodes),
			true
		);
	}

}
