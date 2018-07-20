<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use Generator;
use PHP_CodeSniffer\Files\File;
use const T_ANON_CLASS;
use const T_STRING;
use const T_USE;
use function array_map;
use function iterator_to_array;
use function sprintf;

class ClassHelper
{

	public static function getFullyQualifiedName(File $codeSnifferFile, int $classPointer): string
	{
		$className = self::getName($codeSnifferFile, $classPointer);

		$tokens = $codeSnifferFile->getTokens();
		if ($tokens[$classPointer]['code'] === T_ANON_CLASS) {
			return $className;
		}

		$name = sprintf('%s%s', NamespaceHelper::NAMESPACE_SEPARATOR, $className);
		$namespace = NamespaceHelper::findCurrentNamespaceName($codeSnifferFile, $classPointer);
		return $namespace !== null ? sprintf('%s%s%s', NamespaceHelper::NAMESPACE_SEPARATOR, $namespace, $name) : $name;
	}

	public static function getName(File $codeSnifferFile, int $classPointer): string
	{
		$tokens = $codeSnifferFile->getTokens();

		if ($tokens[$classPointer]['code'] === T_ANON_CLASS) {
			return 'class@anonymous';
		}

		return $tokens[TokenHelper::findNext($codeSnifferFile, T_STRING, $classPointer + 1, $tokens[$classPointer]['scope_opener'])]['content'];
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $codeSnifferFile
	 * @return string[]
	 */
	public static function getAllNames(File $codeSnifferFile): array
	{
		$previousClassPointer = 0;

		return array_map(
			function (int $classPointer) use ($codeSnifferFile): string {
				return self::getName($codeSnifferFile, $classPointer);
			},
			iterator_to_array(self::getAllClassPointers($codeSnifferFile, $previousClassPointer))
		);
	}

	private static function getAllClassPointers(File $codeSnifferFile, int &$previousClassPointer): Generator
	{
		do {
			$nextClassPointer = TokenHelper::findNext($codeSnifferFile, TokenHelper::$typeKeywordTokenCodes, $previousClassPointer + 1);
			if ($nextClassPointer === null) {
				break;
			}

			$previousClassPointer = $nextClassPointer;
			yield $nextClassPointer;
		} while (true);
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $classPointer
	 * @return int[]
	 */
	public static function getTraitUsePointers(File $phpcsFile, int $classPointer): array
	{
		$useStatements = [];

		$tokens = $phpcsFile->getTokens();

		$scopeLevel = $tokens[$classPointer]['level'] + 1;
		for ($i = $tokens[$classPointer]['scope_opener'] + 1; $i < $tokens[$classPointer]['scope_closer']; $i++) {
			if ($tokens[$i]['code'] !== T_USE) {
				continue;
			}

			if ($tokens[$i]['level'] !== $scopeLevel) {
				continue;
			}

			$useStatements[] = $i;
		}

		return $useStatements;
	}

}
