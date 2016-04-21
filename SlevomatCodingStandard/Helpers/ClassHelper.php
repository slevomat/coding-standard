<?php

namespace SlevomatCodingStandard\Helpers;

class ClassHelper
{

	/**
	 * @param \PHP_CodeSniffer_File $codeSnifferFile
	 * @param integer $classPointer
	 * @return string
	 */
	public static function getFullyQualifiedName(\PHP_CodeSniffer_File $codeSnifferFile, $classPointer)
	{
		$name = sprintf('%s%s', NamespaceHelper::NAMESPACE_SEPARATOR, self::getName($codeSnifferFile, $classPointer));
		$namespace = NamespaceHelper::findCurrentNamespaceName($codeSnifferFile, $classPointer);
		return $namespace !== null ? sprintf('%s%s%s', NamespaceHelper::NAMESPACE_SEPARATOR, $namespace, $name) : $name;
	}

	/**
	 * @param \PHP_CodeSniffer_File $codeSnifferFile
	 * @param integer $classPointer
	 * @return string
	 */
	public static function getName(\PHP_CodeSniffer_File $codeSnifferFile, $classPointer)
	{
		$tokens = $codeSnifferFile->getTokens();
		return $tokens[$codeSnifferFile->findNext(T_STRING, $classPointer + 1, $tokens[$classPointer]['scope_opener'])]['content'];
	}

}
