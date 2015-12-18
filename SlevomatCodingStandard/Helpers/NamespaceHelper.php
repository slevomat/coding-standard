<?php

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer_File;

/**
 * Terms "unqualified", "qualified" and "fully qualified" have the same meaning as described here:
 * http://php.net/manual/en/language.namespaces.rules.php
 *
 * "Canonical" is a fully qualified name without the leading backslash.
 */
class NamespaceHelper
{

	const NAMESPACE_SEPARATOR = '\\';

	/**
	 * @param string $typeName
	 * @return boolean
	 */
	public static function isFullyQualifiedName($typeName)
	{
		return StringHelper::startsWith($typeName, self::NAMESPACE_SEPARATOR);
	}

	/**
	 * @param string $typeName
	 * @return boolean
	 */
	public static function hasNamespace($typeName)
	{
		$parts = self::getNameParts($typeName);

		return count($parts) > 1;
	}

	/**
	 * @param string $name
	 * @return string[]
	 */
	public static function getNameParts($name)
	{
		$name = self::normalizeToCanonicalName($name);

		return explode(self::NAMESPACE_SEPARATOR, $name);
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param integer $anyPointer any pointer type where the search begins from (backwards)
	 * @return string|null
	 */
	public static function findCurrentNamespaceName(PHP_CodeSniffer_File $phpcsFile, $anyPointer)
	{
		$namespacePointer = $phpcsFile->findPrevious(T_NAMESPACE, $anyPointer);
		if ($namespacePointer === false) {
			return null;
		}

		$namespaceNameStartPointer = TokenHelper::findNextNonWhitespace($phpcsFile, $namespacePointer + 1);
		$namespaceNameEndPointer = TokenHelper::findNextExcluding($phpcsFile, TokenHelper::$nameTokenCodes, $namespaceNameStartPointer + 1);

		return TokenHelper::getContent($phpcsFile, $namespaceNameStartPointer, $namespaceNameEndPointer);
	}

	/**
	 * @param string $name
	 * @return string
	 */
	public static function getUnqualifiedNameFromFullyQualifiedName($name)
	{
		$parts = self::getNameParts($name);
		return $parts[count($parts) - 1];
	}

	/**
	 * @param string $name
	 * @return boolean
	 */
	public static function isQualifiedName($name)
	{
		return strpos($name, self::NAMESPACE_SEPARATOR) !== false;
	}

	/**
	 * @param string $fullyQualifiedName
	 * @return string
	 */
	public static function normalizeToCanonicalName($fullyQualifiedName)
	{
		return ltrim($fullyQualifiedName, self::NAMESPACE_SEPARATOR);
	}

	/**
	 * @param string $typeName
	 * @param string $namespace
	 * @return boolean
	 */
	public static function isTypeInNamespace($typeName, $namespace)
	{
		return StringHelper::startsWith(
			self::normalizeToCanonicalName($typeName) . '\\',
			$namespace . '\\'
		);
	}

}
