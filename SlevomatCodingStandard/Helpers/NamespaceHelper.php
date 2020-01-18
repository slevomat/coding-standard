<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;
use function array_slice;
use function count;
use function explode;
use function implode;
use function ltrim;
use function sprintf;
use function strpos;
use const T_NAMESPACE;

/**
 * Terms "unqualified", "qualified" and "fully qualified" have the same meaning as described here:
 * http://php.net/manual/en/language.namespaces.rules.php
 *
 * "Canonical" is a fully qualified name without the leading backslash.
 */
class NamespaceHelper
{

	public const NAMESPACE_SEPARATOR = '\\';

	public static function isFullyQualifiedName(string $typeName): bool
	{
		return StringHelper::startsWith($typeName, self::NAMESPACE_SEPARATOR);
	}

	public static function hasNamespace(string $typeName): bool
	{
		$parts = self::getNameParts($typeName);

		return count($parts) > 1;
	}

	/**
	 * @param string $name
	 * @return string[]
	 */
	public static function getNameParts(string $name): array
	{
		$name = self::normalizeToCanonicalName($name);

		return explode(self::NAMESPACE_SEPARATOR, $name);
	}

	public static function findCurrentNamespaceName(File $phpcsFile, int $anyPointer): ?string
	{
		$namespacePointer = TokenHelper::findPrevious($phpcsFile, T_NAMESPACE, $anyPointer);
		if ($namespacePointer === null) {
			return null;
		}

		/** @var int $namespaceNameStartPointer */
		$namespaceNameStartPointer = TokenHelper::findNextEffective($phpcsFile, $namespacePointer + 1);
		$namespaceNameEndPointer = TokenHelper::findNextExcluding($phpcsFile, TokenHelper::$nameTokenCodes, $namespaceNameStartPointer + 1) - 1;

		return TokenHelper::getContent($phpcsFile, $namespaceNameStartPointer, $namespaceNameEndPointer);
	}

	public static function getUnqualifiedNameFromFullyQualifiedName(string $name): string
	{
		$parts = self::getNameParts($name);
		return $parts[count($parts) - 1];
	}

	public static function isQualifiedName(string $name): bool
	{
		return strpos($name, self::NAMESPACE_SEPARATOR) !== false;
	}

	public static function normalizeToCanonicalName(string $fullyQualifiedName): string
	{
		return ltrim($fullyQualifiedName, self::NAMESPACE_SEPARATOR);
	}

	public static function isTypeInNamespace(string $typeName, string $namespace): bool
	{
		return StringHelper::startsWith(
			self::normalizeToCanonicalName($typeName) . '\\',
			$namespace . '\\'
		);
	}

	public static function resolveClassName(File $phpcsFile, string $nameAsReferencedInFile, int $currentPointer): string
	{
		return self::resolveName($phpcsFile, $nameAsReferencedInFile, ReferencedName::TYPE_DEFAULT, $currentPointer);
	}

	public static function resolveName(File $phpcsFile, string $nameAsReferencedInFile, string $type, int $currentPointer): string
	{
		if (self::isFullyQualifiedName($nameAsReferencedInFile)) {
			return $nameAsReferencedInFile;
		}

		$useStatements = UseStatementHelper::getUseStatementsForPointer($phpcsFile, $currentPointer);

		$uniqueId = UseStatement::getUniqueId($type, self::normalizeToCanonicalName($nameAsReferencedInFile));

		if (isset($useStatements[$uniqueId])) {
			return sprintf('%s%s', self::NAMESPACE_SEPARATOR, $useStatements[$uniqueId]->getFullyQualifiedTypeName());
		}

		$nameParts = self::getNameParts($nameAsReferencedInFile);
		$firstPartUniqueId = UseStatement::getUniqueId($type, $nameParts[0]);
		if (count($nameParts) > 1 && isset($useStatements[$firstPartUniqueId])) {
			return sprintf('%s%s%s%s', self::NAMESPACE_SEPARATOR, $useStatements[$firstPartUniqueId]->getFullyQualifiedTypeName(), self::NAMESPACE_SEPARATOR, implode(self::NAMESPACE_SEPARATOR, array_slice($nameParts, 1)));
		}

		$name = sprintf('%s%s', self::NAMESPACE_SEPARATOR, $nameAsReferencedInFile);
		$namespaceName = self::findCurrentNamespaceName($phpcsFile, $currentPointer);
		if ($namespaceName !== null) {
			$name = sprintf('%s%s%s', self::NAMESPACE_SEPARATOR, $namespaceName, $name);
		}
		return $name;
	}

}
