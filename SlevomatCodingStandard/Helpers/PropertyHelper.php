<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;
use function array_keys;
use function array_reverse;
use function in_array;
use function sprintf;
use const T_ANON_CLASS;
use const T_NULLABLE;
use const T_OPEN_CURLY_BRACKET;
use const T_OPEN_PARENTHESIS;
use const T_PRIVATE;
use const T_PROTECTED;
use const T_PUBLIC;
use const T_SEMICOLON;
use const T_STATIC;
use const T_VAR;

class PropertyHelper
{

	public static function isProperty(File $phpcsFile, int $variablePointer): bool
	{
		$previousPointer = TokenHelper::findPrevious($phpcsFile, [T_OPEN_PARENTHESIS, T_OPEN_CURLY_BRACKET, T_SEMICOLON, T_PRIVATE, T_PROTECTED, T_PUBLIC, T_VAR], $variablePointer - 1);

		return $previousPointer !== null && in_array($phpcsFile->getTokens()[$previousPointer]['code'], [T_PRIVATE, T_PROTECTED, T_PUBLIC, T_VAR], true);
	}

	public static function findTypeHint(File $phpcsFile, int $propertyPointer): ?PropertyTypeHint
	{
		$tokens = $phpcsFile->getTokens();

		$propertyStartPointer = TokenHelper::findPrevious($phpcsFile, [T_PRIVATE, T_PROTECTED, T_PUBLIC, T_VAR, T_STATIC], $propertyPointer - 1);

		$typeHintEndPointer = TokenHelper::findPrevious($phpcsFile, TokenHelper::$typeHintTokenCodes, $propertyPointer - 1, $propertyStartPointer);
		if ($typeHintEndPointer === null) {
			return null;
		}

		$typeHintStartPointer = TokenHelper::findPreviousExcluding($phpcsFile, TokenHelper::$typeHintTokenCodes, $typeHintEndPointer, $propertyStartPointer) + 1;

		$previousPointer = TokenHelper::findPreviousEffective($phpcsFile, $typeHintStartPointer - 1, $propertyStartPointer);
		$nullabilitySymbolPointer = $previousPointer !== null && $tokens[$previousPointer]['code'] === T_NULLABLE ? $previousPointer : null;

		return new PropertyTypeHint(
			TokenHelper::getContent($phpcsFile, $typeHintStartPointer, $typeHintEndPointer),
			$nullabilitySymbolPointer !== null,
			$nullabilitySymbolPointer ?? $typeHintStartPointer,
			$typeHintEndPointer
		);
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
