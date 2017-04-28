<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

/**
 * Terms "unqualified", "qualified" and "fully qualified" have the same meaning as described here:
 * http://php.net/manual/en/language.namespaces.rules.php
 *
 * "Canonical" is a fully qualified name without the leading backslash.
 */
class NamespaceHelper
{

	const NAMESPACE_SEPARATOR = '\\';

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

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $anyPointer any pointer type where the search begins from (backwards)
	 * @return string|null
	 */
	public static function findCurrentNamespaceName(\PHP_CodeSniffer\Files\File $phpcsFile, int $anyPointer)
	{
		$namespacePointer = $phpcsFile->findPrevious(T_NAMESPACE, $anyPointer);
		if ($namespacePointer === false) {
			return null;
		}

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

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param string $nameAsReferencedInFile
	 * @param \SlevomatCodingStandard\Helpers\UseStatement[] $useStatements
	 * @param int $currentPointer
	 * @return string
	 */
	public static function resolveName(
		\PHP_CodeSniffer\Files\File $phpcsFile,
		string $nameAsReferencedInFile,
		array $useStatements,
		int $currentPointer
	): string
	{
		if (self::isFullyQualifiedName($nameAsReferencedInFile)) {
			return $nameAsReferencedInFile;
		}

		$normalizedName = UseStatement::normalizedNameAsReferencedInFile(self::normalizeToCanonicalName($nameAsReferencedInFile));

		if (isset($useStatements[$normalizedName])) {
			return sprintf('%s%s', self::NAMESPACE_SEPARATOR, $useStatements[$normalizedName]->getFullyQualifiedTypeName());
		}

		$nameParts = self::getNameParts($nameAsReferencedInFile);
		$normalizedNameFirstPart = UseStatement::normalizedNameAsReferencedInFile($nameParts[0]);
		if (count($nameParts) > 1 && isset($useStatements[$normalizedNameFirstPart])) {
			return sprintf('%s%s%s%s', self::NAMESPACE_SEPARATOR, $useStatements[$normalizedNameFirstPart]->getFullyQualifiedTypeName(), self::NAMESPACE_SEPARATOR, implode(self::NAMESPACE_SEPARATOR, array_slice($nameParts, 1)));
		}

		$name = sprintf('%s%s', self::NAMESPACE_SEPARATOR, $nameAsReferencedInFile);
		$namespaceName = self::findCurrentNamespaceName($phpcsFile, $currentPointer);
		if ($namespaceName !== null) {
			$name = sprintf('%s%s%s', self::NAMESPACE_SEPARATOR, $namespaceName, $name);
		}
		return $name;
	}

}
