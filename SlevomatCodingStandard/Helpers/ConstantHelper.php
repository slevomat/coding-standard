<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class ConstantHelper
{

	public static function getName(\PHP_CodeSniffer\Files\File $codeSnifferFile, int $constantPointer): string
	{
		$tokens = $codeSnifferFile->getTokens();
		return $tokens[TokenHelper::findNext($codeSnifferFile, T_STRING, $constantPointer + 1)]['content'];
	}

	public static function getFullyQualifiedName(\PHP_CodeSniffer\Files\File $codeSnifferFile, int $constantPointer): string
	{
		$name = self::getName($codeSnifferFile, $constantPointer);
		$namespace = NamespaceHelper::findCurrentNamespaceName($codeSnifferFile, $constantPointer);

		return $namespace !== null ? sprintf('%s%s%s%s', NamespaceHelper::NAMESPACE_SEPARATOR, $namespace, NamespaceHelper::NAMESPACE_SEPARATOR, $name) : $name;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $codeSnifferFile
	 * @return string[]
	 */
	public static function getAllNames(\PHP_CodeSniffer\Files\File $codeSnifferFile): array
	{
		$previousConstantPointer = 0;

		return array_map(
			function (int $constantPointer) use ($codeSnifferFile): string {
				return self::getName($codeSnifferFile, $constantPointer);
			},
			array_filter(
				iterator_to_array(self::getAllConstantPointers($codeSnifferFile, $previousConstantPointer)),
				function (int $constantPointer) use ($codeSnifferFile): bool {
					foreach (array_reverse($codeSnifferFile->getTokens()[$constantPointer]['conditions']) as $conditionTokenCode) {
						if ($conditionTokenCode === T_NAMESPACE) {
							return true;
						}

						return false;
					}

					return true;
				}
			)
		);
	}

	private static function getAllConstantPointers(\PHP_CodeSniffer\Files\File $codeSnifferFile, int &$previousConstantPointer): \Generator
	{
		do {
			$nextConstantPointer = TokenHelper::findNext($codeSnifferFile, T_CONST, $previousConstantPointer + 1);
			if ($nextConstantPointer === null) {
				break;
			}

			$previousConstantPointer = $nextConstantPointer;
			yield $nextConstantPointer;
		} while (true);
	}

}
