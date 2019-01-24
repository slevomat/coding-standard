<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use Generator;
use PHP_CodeSniffer\Files\File;
use function array_map;
use function iterator_to_array;
use function sprintf;
use const T_ANON_CLASS;
use const T_FINAL;
use const T_STRING;
use const T_USE;

class ClassHelper
{

	public static function isFinal(File $phpcsFile, int $classPointer): bool
	{
		return $phpcsFile->getTokens()[TokenHelper::findPreviousEffective($phpcsFile, $classPointer - 1)]['code'] === T_FINAL;
	}

	public static function getFullyQualifiedName(File $phpcsFile, int $classPointer): string
	{
		$className = self::getName($phpcsFile, $classPointer);

		$tokens = $phpcsFile->getTokens();
		if ($tokens[$classPointer]['code'] === T_ANON_CLASS) {
			return $className;
		}

		$name = sprintf('%s%s', NamespaceHelper::NAMESPACE_SEPARATOR, $className);
		$namespace = NamespaceHelper::findCurrentNamespaceName($phpcsFile, $classPointer);
		return $namespace !== null ? sprintf('%s%s%s', NamespaceHelper::NAMESPACE_SEPARATOR, $namespace, $name) : $name;
	}

	public static function getName(File $phpcsFile, int $classPointer): string
	{
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$classPointer]['code'] === T_ANON_CLASS) {
			return 'class@anonymous';
		}

		return $tokens[TokenHelper::findNext($phpcsFile, T_STRING, $classPointer + 1, $tokens[$classPointer]['scope_opener'])]['content'];
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @return string[]
	 */
	public static function getAllNames(File $phpcsFile): array
	{
		$previousClassPointer = 0;

		return array_map(
			function (int $classPointer) use ($phpcsFile): string {
				return self::getName($phpcsFile, $classPointer);
			},
			iterator_to_array(self::getAllClassPointers($phpcsFile, $previousClassPointer))
		);
	}

	private static function getAllClassPointers(File $phpcsFile, int &$previousClassPointer): Generator
	{
		do {
			$nextClassPointer = TokenHelper::findNext($phpcsFile, TokenHelper::$typeKeywordTokenCodes, $previousClassPointer + 1);
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
