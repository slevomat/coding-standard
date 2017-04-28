<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class ClassHelper
{

	public static function getFullyQualifiedName(\PHP_CodeSniffer\Files\File $codeSnifferFile, int $classPointer): string
	{
		$name = sprintf('%s%s', NamespaceHelper::NAMESPACE_SEPARATOR, self::getName($codeSnifferFile, $classPointer));
		$namespace = NamespaceHelper::findCurrentNamespaceName($codeSnifferFile, $classPointer);
		return $namespace !== null ? sprintf('%s%s%s', NamespaceHelper::NAMESPACE_SEPARATOR, $namespace, $name) : $name;
	}

	public static function getName(\PHP_CodeSniffer\Files\File $codeSnifferFile, int $classPointer): string
	{
		$tokens = $codeSnifferFile->getTokens();
		return $tokens[$codeSnifferFile->findNext(T_STRING, $classPointer + 1, $tokens[$classPointer]['scope_opener'])]['content'];
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $codeSnifferFile
	 * @return string[]
	 */
	public static function getAllNames(\PHP_CodeSniffer\Files\File $codeSnifferFile): array
	{
		$previousClassPointer = 0;

		return array_map(
			function (int $classPointer) use ($codeSnifferFile): string {
				return self::getName($codeSnifferFile, $classPointer);
			},
			iterator_to_array(self::getAllClassPointers($codeSnifferFile, $previousClassPointer))
		);
	}

	private static function getAllClassPointers(\PHP_CodeSniffer\Files\File $codeSnifferFile, int &$previousClassPointer): \Generator
	{
		do {
			$nextClassPointer = $codeSnifferFile->findNext(TokenHelper::$typeKeywordTokenCodes, $previousClassPointer + 1);
			if ($nextClassPointer !== false) {
				$previousClassPointer = $nextClassPointer;
				yield $nextClassPointer;
			}
		} while ($nextClassPointer !== false);
	}

}
