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
 * - method name alias imported from trait
 */
class ReferencedNameHelper
{

	/** @var \SlevomatCodingStandard\Helpers\ReferencedName[][] Cached data for method getAllReferencedNames, cacheKey(string) => pointer(integer) => name(string) */
	private static $allReferencedTypesCache = [];

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $openTagPointer
	 * @return \SlevomatCodingStandard\Helpers\ReferencedName[] referenced names
	 */
	public static function getAllReferencedNames(\PHP_CodeSniffer\Files\File $phpcsFile, int $openTagPointer): array
	{
		$cacheKey = $phpcsFile->getFilename() . '-' . $openTagPointer;

		$fixerLoops = $phpcsFile->fixer !== null ? $phpcsFile->fixer->loops : null;
		if ($fixerLoops !== null) {
			$cacheKey .= '-loop' . $fixerLoops;
			if ($fixerLoops > 0) {
				unset(self::$allReferencedTypesCache[$cacheKey . '-loop' . ($fixerLoops - 1)]);
			}
		}

		if (!isset(self::$allReferencedTypesCache[$cacheKey])) {
			self::$allReferencedTypesCache[$cacheKey] = self::createAllReferencedNames($phpcsFile, $openTagPointer);
		}

		return self::$allReferencedTypesCache[$cacheKey];
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $openTagPointer
	 * @return \SlevomatCodingStandard\Helpers\ReferencedName[] referenced names
	 */
	private static function createAllReferencedNames(\PHP_CodeSniffer\Files\File $phpcsFile, int $openTagPointer): array
	{
		$tokens = $phpcsFile->getTokens();

		$beginSearchAtPointer = $openTagPointer + 1;
		$searchTypes = array_merge([T_RETURN_TYPE], TokenHelper::$nameTokenCodes);
		$types = [];

		while (true) {
			$nameStartPointer = TokenHelper::findNext($phpcsFile, $searchTypes, $beginSearchAtPointer);
			if ($nameStartPointer === null) {
				break;
			}

			if (!self::isReferencedName($phpcsFile, $nameStartPointer)) {
				$beginSearchAtPointer = TokenHelper::findNextExcluding(
					$phpcsFile,
					array_merge(TokenHelper::$ineffectiveTokenCodes, [T_RETURN_TYPE], TokenHelper::$nameTokenCodes),
					$nameStartPointer + 1
				);
				continue;
			}

			$nameEndPointer = self::getReferencedNameEndPointer($phpcsFile, $nameStartPointer);

			$nextTokenAfterEndPointer = TokenHelper::findNextEffective($phpcsFile, $nameEndPointer + 1);
			$previousTokenBeforeStartPointer = TokenHelper::findPreviousEffective($phpcsFile, $nameStartPointer - 1);
			$type = ReferencedName::TYPE_DEFAULT;
			if ($nextTokenAfterEndPointer !== null && $previousTokenBeforeStartPointer !== null) {
				if ($tokens[$nextTokenAfterEndPointer]['code'] === T_OPEN_PARENTHESIS) {
					if ($tokens[$previousTokenBeforeStartPointer]['code'] !== T_NEW) {
						$type = ReferencedName::TYPE_FUNCTION;
					}
				} elseif (
					!in_array($tokens[$nextTokenAfterEndPointer]['code'], [
						T_VARIABLE,
						T_ELLIPSIS, // Variadic parameter
						T_BITWISE_AND, // Parameter by reference
					], true)
				) {
					if (
						!in_array($tokens[$previousTokenBeforeStartPointer]['code'], [
							T_EXTENDS,
							T_IMPLEMENTS,
							T_INSTANCEOF,
							T_USE, // Trait
							T_NEW,
							T_COLON, // Return type hint
							T_NULLABLE, // Nullable type hint
						], true)
						&& $tokens[$nextTokenAfterEndPointer]['code'] !== T_DOUBLE_COLON
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
								], true)
							) {
								$type = ReferencedName::TYPE_CONSTANT;
							}
						} elseif ($tokens[$previousTokenBeforeStartPointer]['code'] === T_BITWISE_OR
							|| $tokens[$previousTokenBeforeStartPointer]['code'] === T_OPEN_PARENTHESIS
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
			$types[] = new ReferencedName(TokenHelper::getContent($phpcsFile, $nameStartPointer, $nameEndPointer), $nameStartPointer, $nameEndPointer, $type);
			$beginSearchAtPointer = $nameEndPointer + 1;
		}
		return $types;
	}

	public static function getReferencedNameEndPointer(\PHP_CodeSniffer\Files\File $phpcsFile, int $startPointer): int
	{
		$pointerAfterEndPointer = TokenHelper::findNextExcluding($phpcsFile, array_merge([T_RETURN_TYPE], TokenHelper::$nameTokenCodes), $startPointer + 1);
		return $pointerAfterEndPointer === null ? $startPointer : $pointerAfterEndPointer - 1;
	}

	private static function isReferencedName(\PHP_CodeSniffer\Files\File $phpcsFile, int $startPointer): bool
	{
		$tokens = $phpcsFile->getTokens();

		$nextPointer = TokenHelper::findNextEffective($phpcsFile, $startPointer + 1);
		$previousPointer = TokenHelper::findPreviousEffective($phpcsFile, $startPointer - 1);

		if ($tokens[$nextPointer]['code'] === T_DOUBLE_COLON) {
			return $tokens[$previousPointer]['code'] !== T_OBJECT_OPERATOR;
		}

		if (count($tokens[$startPointer]['conditions']) > 0 && array_values(array_reverse($tokens[$startPointer]['conditions']))[0] === T_USE) {
			// Method imported from trait
			return false;
		}

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
			$classPointer = TokenHelper::findPrevious($phpcsFile, [T_CLASS, T_TRAIT], $startPointer - 1);
			if ($classPointer !== null) {
				$classToken = $tokens[$classPointer];
				return $startPointer > $classToken['scope_opener'] && $startPointer < $classToken['scope_closer'];
			}

			return false;
		} elseif ($previousToken['code'] === T_OPEN_PARENTHESIS && isset($previousToken['parenthesis_owner']) && $tokens[$previousToken['parenthesis_owner']]['code'] === T_DECLARE) {
			return false;
		} elseif ($previousToken['code'] === T_COMMA && TokenHelper::findPreviousLocal($phpcsFile, T_DECLARE, $previousPointer - 1) !== null) {
			return false;
		} elseif ($previousToken['code'] === T_COMMA) {
			$constPointer = TokenHelper::findPreviousLocal($phpcsFile, T_CONST, $previousPointer - 1);
			if ($constPointer !== null && TokenHelper::findNext($phpcsFile, [T_OPEN_SHORT_ARRAY, T_ARRAY], $constPointer + 1, $startPointer) === null) {
				return false;
			}
		} elseif ($previousToken['code'] === T_BITWISE_AND && TokenHelper::findPreviousLocal($phpcsFile, [T_FUNCTION], $previousPointer - 1) !== null) {
			return false;
		} elseif ($previousToken['code'] === T_GOTO) {
			return false;
		}

		$isProbablyReferencedName = !in_array(
			$previousToken['code'],
			array_merge($skipTokenCodes, TokenHelper::$typeKeywordTokenCodes),
			true
		);

		if (!$isProbablyReferencedName) {
			return false;
		}

		$endPointer = self::getReferencedNameEndPointer($phpcsFile, $startPointer);
		$referencedName = TokenHelper::getContent($phpcsFile, $startPointer, $endPointer);

		return !TypeHintHelper::isSimpleTypeHint($referencedName) && $referencedName !== 'object';
	}

}
