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

	/**
	 * @param \PHP_CodeSniffer_File $codeSnifferFile
	 * @return string[]
	 */
	public static function getAllNames(\PHP_CodeSniffer_File $codeSnifferFile): array
	{
		$getAllClassPointers = function (\PHP_CodeSniffer_File $codeSnifferFile, &$previousClassPointer): \Generator {
			$previousClassPointer = $nextClassPointer = $codeSnifferFile->findNext(TokenHelper::$typeKeywordTokenCodes, $previousClassPointer + 1);
			if ($nextClassPointer !== null) {
				yield $nextClassPointer;
			}
		};
		$previousClassPointer = 0;

		return array_map(
			function (int $classPointer) use ($codeSnifferFile) {
				return self::getName($codeSnifferFile, $classPointer);
			},
			iterator_to_array($getAllClassPointers($codeSnifferFile, $previousClassPointer))
		);
	}

}
