<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;
use function array_keys;
use function array_merge;
use function array_reverse;
use function in_array;
use function sprintf;
use const T_ANON_CLASS;
use const T_PRIVATE;
use const T_PROTECTED;
use const T_PUBLIC;
use const T_STATIC;
use const T_VAR;

class PropertyHelper
{

	public static function isProperty(File $phpcsFile, int $variablePointer): bool
	{
		$propertyDeterminingPointer = TokenHelper::findPreviousExcluding(
			$phpcsFile,
			array_merge([T_STATIC], TokenHelper::$ineffectiveTokenCodes),
			$variablePointer - 1
		);

		return in_array($phpcsFile->getTokens()[$propertyDeterminingPointer]['code'], [
			T_PRIVATE,
			T_PROTECTED,
			T_PUBLIC,
			T_VAR,
		], true);
	}

	public static function getFullyQualifiedName(File $phpcsFile, int $propertyPointer): string
	{
		$propertyToken = $phpcsFile->getTokens()[$propertyPointer];
		$propertyName = $propertyToken['content'];

		$classPointer = array_reverse(array_keys($propertyToken['conditions']))[0];
		if ($phpcsFile->getTokens()[$classPointer]['code'] === T_ANON_CLASS) {
			return sprintf('class@anonymous::%s', $propertyName);
		}

		$name = sprintf('%s%s::%s', NamespaceHelper::NAMESPACE_SEPARATOR, ClassHelper::getName($phpcsFile, $classPointer), $propertyName);
		$namespace = NamespaceHelper::findCurrentNamespaceName($phpcsFile, $propertyPointer);
		return $namespace !== null ? sprintf('%s%s%s', NamespaceHelper::NAMESPACE_SEPARATOR, $namespace, $name) : $name;
	}

}
