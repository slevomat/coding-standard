<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;
use function array_key_exists;
use function array_merge;
use function array_reverse;
use function count;
use function current;
use function in_array;
use const T_ANON_CLASS;
use const T_AS;
use const T_COMMA;
use const T_NAMESPACE;
use const T_OPEN_PARENTHESIS;
use const T_OPEN_TAG;
use const T_SEMICOLON;
use const T_STRING;
use const T_USE;

class UseStatementHelper
{

	/** @var array<string, array<int, array<string, \SlevomatCodingStandard\Helpers\UseStatement>>> */
	private static $fileUseStatements = [];

	public static function isAnonymousFunctionUse(File $phpcsFile, int $usePointer): bool
	{
		$tokens = $phpcsFile->getTokens();
		$nextPointer = TokenHelper::findNextEffective($phpcsFile, $usePointer + 1);
		$nextToken = $tokens[$nextPointer];

		return $nextToken['code'] === T_OPEN_PARENTHESIS;
	}

	public static function isTraitUse(File $phpcsFile, int $usePointer): bool
	{
		$typePointer = TokenHelper::findPrevious($phpcsFile, array_merge(TokenHelper::$typeKeywordTokenCodes, [T_ANON_CLASS]), $usePointer);
		if ($typePointer !== null) {
			$tokens = $phpcsFile->getTokens();
			$typeToken = $tokens[$typePointer];
			$openerPointer = $typeToken['scope_opener'];
			$closerPointer = $typeToken['scope_closer'];

			return $usePointer > $openerPointer && $usePointer < $closerPointer
				&& !self::isAnonymousFunctionUse($phpcsFile, $usePointer);
		}

		return false;
	}

	public static function getAlias(File $phpcsFile, int $usePointer): ?string
	{
		$endPointer = TokenHelper::findNext($phpcsFile, [T_SEMICOLON, T_COMMA], $usePointer + 1);
		$asPointer = TokenHelper::findNext($phpcsFile, T_AS, $usePointer + 1, $endPointer);

		if ($asPointer === null) {
			return null;
		}

		$tokens = $phpcsFile->getTokens();
		return $tokens[TokenHelper::findNext($phpcsFile, T_STRING, $asPointer + 1)]['content'];
	}

	public static function getNameAsReferencedInClassFromUse(File $phpcsFile, int $usePointer): string
	{
		$alias = self::getAlias($phpcsFile, $usePointer);
		if ($alias !== null) {
			return $alias;
		}

		$name = self::getFullyQualifiedTypeNameFromUse($phpcsFile, $usePointer);
		return NamespaceHelper::getUnqualifiedNameFromFullyQualifiedName($name);
	}

	/**
	 * @deprecated
	 * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $openTagPointer
	 * @return array<int, array<string, \SlevomatCodingStandard\Helpers\UseStatement>>
	 */
	public static function getUseStatements(File $phpcsFile, int $openTagPointer): array
	{
		return self::getFileUseStatements($phpcsFile);
	}

	public static function getFullyQualifiedTypeNameFromUse(File $phpcsFile, int $usePointer): string
	{
		$tokens = $phpcsFile->getTokens();

		$nameEndPointer = TokenHelper::findNext($phpcsFile, [T_SEMICOLON, T_AS, T_COMMA], $usePointer + 1) - 1;
		if (in_array($tokens[$nameEndPointer]['code'], TokenHelper::$ineffectiveTokenCodes, true)) {
			$nameEndPointer = TokenHelper::findPreviousEffective($phpcsFile, $nameEndPointer);
		}
		$nameStartPointer = TokenHelper::findPreviousExcluding($phpcsFile, TokenHelper::$nameTokenCodes, $nameEndPointer - 1) + 1;

		$name = TokenHelper::getContent($phpcsFile, $nameStartPointer, $nameEndPointer);

		return NamespaceHelper::normalizeToCanonicalName($name);
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $pointer
	 * @return array<string, \SlevomatCodingStandard\Helpers\UseStatement>
	 */
	public static function getUseStatementsForPointer(File $phpcsFile, int $pointer): array
	{
		$allUseStatements = self::getFileUseStatements($phpcsFile);

		if (count($allUseStatements) === 1) {
			return current($allUseStatements);
		}

		foreach (array_reverse($allUseStatements, true) as $pointerBeforeUseStatements => $useStatements) {
			if ($pointerBeforeUseStatements < $pointer) {
				return $useStatements;
			}
		}

		return [];
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @return array<int, array<string, \SlevomatCodingStandard\Helpers\UseStatement>>
	 */
	public static function getFileUseStatements(File $phpcsFile): array
	{
		$cacheKey = $phpcsFile->getFilename();

		$fixerLoops = $phpcsFile->fixer !== null ? $phpcsFile->fixer->loops : null;
		if ($fixerLoops !== null) {
			$cacheKey .= '-loop' . $fixerLoops;
			if ($fixerLoops > 0) {
				unset(self::$fileUseStatements[$cacheKey . '-loop' . ($fixerLoops - 1)]);
			}
		}

		if (!array_key_exists($cacheKey, self::$fileUseStatements)) {
			$useStatements = [];
			$tokens = $phpcsFile->getTokens();

			$namespaceAndOpenTagPointers = TokenHelper::findNextAll($phpcsFile, [T_OPEN_TAG, T_NAMESPACE], 0);
			$openTagPointer = $namespaceAndOpenTagPointers[0];

			foreach (self::getUseStatementPointers($phpcsFile, $openTagPointer) as $usePointer) {
				$pointerBeforeUseStatements = $openTagPointer;
				if (count($namespaceAndOpenTagPointers) > 1) {
					foreach (array_reverse($namespaceAndOpenTagPointers) as $namespaceAndOpenTagPointer) {
						if ($namespaceAndOpenTagPointer < $usePointer) {
							$pointerBeforeUseStatements = $namespaceAndOpenTagPointer;
							break;
						}
					}
				}

				$nextTokenFromUsePointer = TokenHelper::findNextEffective($phpcsFile, $usePointer + 1);
				$type = UseStatement::TYPE_DEFAULT;
				if ($tokens[$nextTokenFromUsePointer]['code'] === T_STRING) {
					if ($tokens[$nextTokenFromUsePointer]['content'] === 'const') {
						$type = UseStatement::TYPE_CONSTANT;
					} elseif ($tokens[$nextTokenFromUsePointer]['content'] === 'function') {
						$type = UseStatement::TYPE_FUNCTION;
					}
				}
				$name = self::getNameAsReferencedInClassFromUse($phpcsFile, $usePointer);
				$useStatement = new UseStatement(
					$name,
					self::getFullyQualifiedTypeNameFromUse($phpcsFile, $usePointer),
					$usePointer,
					$type,
					self::getAlias($phpcsFile, $usePointer)
				);
				$useStatements[$pointerBeforeUseStatements][UseStatement::getUniqueId($type, $name)] = $useStatement;
			}

			self::$fileUseStatements[$cacheKey] = $useStatements;
		}

		return self::$fileUseStatements[$cacheKey];
	}

	/**
	 * Searches for all use statements in a file, skips bodies of classes and traits.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $openTagPointer
	 * @return int[]
	 */
	private static function getUseStatementPointers(File $phpcsFile, int $openTagPointer): array
	{
		$tokens = $phpcsFile->getTokens();
		$pointer = $openTagPointer + 1;
		$pointers = [];
		while (true) {
			$typesToFind = array_merge([T_USE], TokenHelper::$typeKeywordTokenCodes);
			$pointer = TokenHelper::findNext($phpcsFile, $typesToFind, $pointer);
			if ($pointer === null) {
				break;
			}

			$token = $tokens[$pointer];
			if (in_array($token['code'], TokenHelper::$typeKeywordTokenCodes, true)) {
				$pointer = $token['scope_closer'] + 1;
				continue;
			}
			if (self::isAnonymousFunctionUse($phpcsFile, $pointer)) {
				$pointer++;
				continue;
			}
			$pointers[] = $pointer;
			$pointer++;
		}
		return $pointers;
	}

}
