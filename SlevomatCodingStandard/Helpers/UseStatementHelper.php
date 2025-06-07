<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;
use function array_key_exists;
use function array_merge;
use function array_reverse;
use function count;
use function current;
use function in_array;
use function strtolower;
use const T_AS;
use const T_CLOSE_CURLY_BRACKET;
use const T_COMMA;
use const T_DECLARE;
use const T_FUNCTION;
use const T_NAMESPACE;
use const T_OPEN_CURLY_BRACKET;
use const T_OPEN_PARENTHESIS;
use const T_OPEN_TAG;
use const T_OPEN_USE_GROUP;
use const T_SEMICOLON;
use const T_STRING;
use const T_USE;

/**
 * @internal
 */
class UseStatementHelper
{

	public static function isImportUse(File $phpcsFile, int $usePointer): bool
	{
		$tokens = $phpcsFile->getTokens();
		$nextPointer = TokenHelper::findNextEffective($phpcsFile, $usePointer + 1);

		// Anonymous function use
		if ($tokens[$nextPointer]['code'] === T_OPEN_PARENTHESIS) {
			return false;
		}

		if (
			$tokens[$nextPointer]['code'] === T_STRING
			&& in_array(strtolower($tokens[$nextPointer]['content']), ['function', 'const'], true)
		) {
			return true;
		}

		$previousPointer = TokenHelper::findPrevious(
			$phpcsFile,
			[T_OPEN_TAG, T_DECLARE, T_NAMESPACE, T_OPEN_CURLY_BRACKET, T_CLOSE_CURLY_BRACKET],
			$usePointer,
		);

		if (in_array($tokens[$previousPointer]['code'], [T_OPEN_TAG, T_DECLARE, T_NAMESPACE], true)) {
			return true;
		}

		if (array_key_exists('scope_condition', $tokens[$previousPointer])) {
			$scopeConditionPointer = $tokens[$previousPointer]['scope_condition'];

			if (
				$tokens[$previousPointer]['code'] === T_OPEN_CURLY_BRACKET
				&& in_array($tokens[$scopeConditionPointer]['code'], TokenHelper::CLASS_TYPE_WITH_ANONYMOUS_CLASS_TOKEN_CODES, true)
			) {
				return false;
			}

			// Trait use after another trait use
			if ($tokens[$scopeConditionPointer]['code'] === T_USE) {
				return false;
			}

			// Trait use after method or import use after function
			if ($tokens[$scopeConditionPointer]['code'] === T_FUNCTION) {
				return ClassHelper::getClassPointer($phpcsFile, $usePointer) === null;
			}
		}

		return true;
	}

	public static function isTraitUse(File $phpcsFile, int $usePointer): bool
	{
		$tokens = $phpcsFile->getTokens();
		$nextPointer = TokenHelper::findNextEffective($phpcsFile, $usePointer + 1);

		// Anonymous function use
		if ($tokens[$nextPointer]['code'] === T_OPEN_PARENTHESIS) {
			return false;
		}

		return !self::isImportUse($phpcsFile, $usePointer);
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

	public static function getFullyQualifiedTypeNameFromUse(File $phpcsFile, int $usePointer): string
	{
		$tokens = $phpcsFile->getTokens();

		$nameEndPointer = TokenHelper::findNext($phpcsFile, [T_SEMICOLON, T_AS, T_COMMA], $usePointer + 1) - 1;
		if (in_array($tokens[$nameEndPointer]['code'], TokenHelper::INEFFECTIVE_TOKEN_CODES, true)) {
			$nameEndPointer = TokenHelper::findPreviousEffective($phpcsFile, $nameEndPointer);
		}
		$nameStartPointer = TokenHelper::findPreviousExcluding($phpcsFile, TokenHelper::NAME_TOKEN_CODES, $nameEndPointer - 1) + 1;

		$name = TokenHelper::getContent($phpcsFile, $nameStartPointer, $nameEndPointer);

		return NamespaceHelper::normalizeToCanonicalName($name);
	}

	/**
	 * @return array<string, UseStatement>
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
	 * @return array<int, array<string, UseStatement>>
	 */
	public static function getFileUseStatements(File $phpcsFile): array
	{
		$lazyValue = static function () use ($phpcsFile): array {
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
				$type = UseStatement::TYPE_CLASS;
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
					self::getAlias($phpcsFile, $usePointer),
				);
				$useStatements[$pointerBeforeUseStatements][UseStatement::getUniqueId($type, $name)] = $useStatement;
			}

			return $useStatements;
		};

		return SniffLocalCache::getAndSetIfNotCached($phpcsFile, 'useStatements', $lazyValue);
	}

	public static function getUseStatementPointer(File $phpcsFile, int $pointer): ?int
	{
		$pointers = self::getUseStatementPointers($phpcsFile, 0);

		foreach (array_reverse($pointers) as $pointerBeforeUseStatements) {
			if ($pointerBeforeUseStatements < $pointer) {
				return $pointerBeforeUseStatements;
			}
		}

		return null;
	}

	/**
	 * Searches for all use statements in a file, skips bodies of classes and traits.
	 *
	 * @return list<int>
	 */
	private static function getUseStatementPointers(File $phpcsFile, int $openTagPointer): array
	{
		$lazy = static function () use ($phpcsFile, $openTagPointer): array {
			$tokens = $phpcsFile->getTokens();
			$pointer = $openTagPointer + 1;
			$pointers = [];
			while (true) {
				$typesToFind = array_merge([T_USE], TokenHelper::CLASS_TYPE_TOKEN_CODES);
				$pointer = TokenHelper::findNext($phpcsFile, $typesToFind, $pointer);
				if ($pointer === null) {
					break;
				}

				$token = $tokens[$pointer];
				if (in_array($token['code'], TokenHelper::CLASS_TYPE_TOKEN_CODES, true)) {
					$pointer = $token['scope_closer'] + 1;
					continue;
				}

				if (self::isGroupUse($phpcsFile, $pointer)) {
					$pointer++;
					continue;
				}

				if (!self::isImportUse($phpcsFile, $pointer)) {
					$pointer++;
					continue;
				}

				$pointers[] = $pointer;
				$pointer++;
			}

			return $pointers;
		};

		return SniffLocalCache::getAndSetIfNotCached($phpcsFile, 'useStatementPointers', $lazy);
	}

	private static function isGroupUse(File $phpcsFile, int $usePointer): bool
	{
		$tokens = $phpcsFile->getTokens();
		$semicolonOrGroupUsePointer = TokenHelper::findNext($phpcsFile, [T_SEMICOLON, T_OPEN_USE_GROUP], $usePointer + 1);

		return $tokens[$semicolonOrGroupUsePointer]['code'] === T_OPEN_USE_GROUP;
	}

}
