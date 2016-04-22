<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class ClassHelper
{

	public static function getFullyQualifiedName(\PHP_CodeSniffer_File $codeSnifferFile, int $classPointer): string
	{
		$name = sprintf('%s%s', NamespaceHelper::NAMESPACE_SEPARATOR, self::getName($codeSnifferFile, $classPointer));
		$namespace = NamespaceHelper::findCurrentNamespaceName($codeSnifferFile, $classPointer);
		return $namespace !== null ? sprintf('%s%s%s', NamespaceHelper::NAMESPACE_SEPARATOR, $namespace, $name) : $name;
	}

	public static function getName(\PHP_CodeSniffer_File $codeSnifferFile, int $classPointer): string
	{
		$tokens = $codeSnifferFile->getTokens();
		return $tokens[$codeSnifferFile->findNext(T_STRING, $classPointer + 1, $tokens[$classPointer]['scope_opener'])]['content'];
	}

}
