<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use Generator;
use PHP_CodeSniffer\Files\File;
use const T_ANON_CLASS;
use const T_BITWISE_AND;
use const T_CLASS;
use const T_COLON;
use const T_COMMA;
use const T_ELLIPSIS;
use const T_EQUAL;
use const T_FUNCTION;
use const T_INTERFACE;
use const T_NULLABLE;
use const T_RETURN;
use const T_SEMICOLON;
use const T_STRING;
use const T_TRAIT;
use const T_VARIABLE;
use const T_YIELD;
use const T_YIELD_FROM;
use function array_filter;
use function array_keys;
use function array_map;
use function array_merge;
use function array_reverse;
use function count;
use function in_array;
use function iterator_to_array;
use function sprintf;

class FunctionHelper
{

	public static function getName(File $codeSnifferFile, int $functionPointer): string
	{
		$tokens = $codeSnifferFile->getTokens();
		return $tokens[TokenHelper::findNext($codeSnifferFile, T_STRING, $functionPointer + 1, $tokens[$functionPointer]['parenthesis_opener'])]['content'];
	}

	public static function getFullyQualifiedName(File $codeSnifferFile, int $functionPointer): string
	{
		$name = self::getName($codeSnifferFile, $functionPointer);
		$namespace = NamespaceHelper::findCurrentNamespaceName($codeSnifferFile, $functionPointer);

		if (self::isMethod($codeSnifferFile, $functionPointer)) {
			foreach (array_reverse($codeSnifferFile->getTokens()[$functionPointer]['conditions'], true) as $conditionPointer => $conditionTokenCode) {
				if ($conditionTokenCode === T_ANON_CLASS) {
					return sprintf('class@anonymous::%s', $name);
				}

				if (in_array($conditionTokenCode, [T_CLASS, T_INTERFACE, T_TRAIT], true)) {
					$name = sprintf('%s%s::%s', NamespaceHelper::NAMESPACE_SEPARATOR, ClassHelper::getName($codeSnifferFile, $conditionPointer), $name);
					break;
				}
			}

			return $namespace !== null ? sprintf('%s%s%s', NamespaceHelper::NAMESPACE_SEPARATOR, $namespace, $name) : $name;
		}

		return $namespace !== null ? sprintf('%s%s%s%s', NamespaceHelper::NAMESPACE_SEPARATOR, $namespace, NamespaceHelper::NAMESPACE_SEPARATOR, $name) : $name;
	}

	public static function isAbstract(File $codeSnifferFile, int $functionPointer): bool
	{
		return !isset($codeSnifferFile->getTokens()[$functionPointer]['scope_opener']);
	}

	public static function isMethod(File $codeSnifferFile, int $functionPointer): bool
	{
		foreach (array_reverse($codeSnifferFile->getTokens()[$functionPointer]['conditions']) as $conditionTokenCode) {
			if (!in_array($conditionTokenCode, [T_CLASS, T_INTERFACE, T_TRAIT, T_ANON_CLASS], true)) {
				continue;
			}

			return true;
		}

		return false;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $codeSnifferFile
	 * @param int $functionPointer
	 * @return string[]
	 */
	public static function getParametersNames(File $codeSnifferFile, int $functionPointer): array
	{
		$tokens = $codeSnifferFile->getTokens();

		$parametersNames = [];
		for ($i = $tokens[$functionPointer]['parenthesis_opener'] + 1; $i < $tokens[$functionPointer]['parenthesis_closer']; $i++) {
			if ($tokens[$i]['code'] !== T_VARIABLE) {
				continue;
			}

			$parametersNames[] = $tokens[$i]['content'];
		}

		return $parametersNames;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $codeSnifferFile
	 * @param int $functionPointer
	 * @return string[]
	 */
	public static function getParametersWithoutTypeHint(File $codeSnifferFile, int $functionPointer): array
	{
		return array_keys(array_filter(self::getParametersTypeHints($codeSnifferFile, $functionPointer), function (?ParameterTypeHint $parameterTypeHint = null): bool {
			return $parameterTypeHint === null;
		}));
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $codeSnifferFile
	 * @param int $functionPointer
	 * @return \SlevomatCodingStandard\Helpers\ParameterTypeHint[]|null[]
	 */
	public static function getParametersTypeHints(File $codeSnifferFile, int $functionPointer): array
	{
		$tokens = $codeSnifferFile->getTokens();

		$parametersTypeHints = [];
		for ($i = $tokens[$functionPointer]['parenthesis_opener'] + 1; $i < $tokens[$functionPointer]['parenthesis_closer']; $i++) {
			if ($tokens[$i]['code'] !== T_VARIABLE) {
				continue;
			}

			$parameterName = $tokens[$i]['content'];
			$typeHint = '';
			$isNullable = false;

			$previousToken = $i;
			do {
				$previousToken = TokenHelper::findPreviousExcluding($codeSnifferFile, array_merge(TokenHelper::$ineffectiveTokenCodes, [T_BITWISE_AND, T_ELLIPSIS]), $previousToken - 1, $tokens[$functionPointer]['parenthesis_opener'] + 1);
				if ($previousToken !== null) {
					$isTypeHint = $tokens[$previousToken]['code'] !== T_COMMA && $tokens[$previousToken]['code'] !== T_NULLABLE;
					if ($tokens[$previousToken]['code'] === T_NULLABLE) {
						$isNullable = true;
					}
				} else {
					$isTypeHint = false;
				}
				if (!$isTypeHint) {
					continue;
				}

				$typeHint = $tokens[$previousToken]['content'] . $typeHint;
			} while ($isTypeHint);

			$equalsPointer = TokenHelper::findNextEffective($codeSnifferFile, $i + 1, $tokens[$functionPointer]['parenthesis_closer']);
			$isOptional = $equalsPointer !== null && $tokens[$equalsPointer]['code'] === T_EQUAL;

			$parametersTypeHints[$parameterName] = $typeHint !== '' ? new ParameterTypeHint($typeHint, $isNullable, $isOptional) : null;
		}

		return $parametersTypeHints;
	}

	public static function returnsValue(File $codeSnifferFile, int $functionPointer): bool
	{
		$tokens = $codeSnifferFile->getTokens();

		$firstPointerInScope = $tokens[$functionPointer]['scope_opener'] + 1;

		for ($i = $firstPointerInScope; $i < $tokens[$functionPointer]['scope_closer']; $i++) {
			if (!in_array($tokens[$i]['code'], [T_YIELD, T_YIELD_FROM], true)) {
				continue;
			}

			if (!ScopeHelper::isInSameScope($codeSnifferFile, $i, $firstPointerInScope)) {
				continue;
			}

			return true;
		}

		for ($i = $firstPointerInScope; $i < $tokens[$functionPointer]['scope_closer']; $i++) {
			if ($tokens[$i]['code'] !== T_RETURN) {
				continue;
			}

			if (!ScopeHelper::isInSameScope($codeSnifferFile, $i, $firstPointerInScope)) {
				continue;
			}

			$nextEffectiveTokenPointer = TokenHelper::findNextEffective($codeSnifferFile, $i + 1);
			return $tokens[$nextEffectiveTokenPointer]['code'] !== T_SEMICOLON;
		}

		return false;
	}

	public static function findReturnTypeHint(File $codeSnifferFile, int $functionPointer): ?ReturnTypeHint
	{
		$tokens = $codeSnifferFile->getTokens();

		$isAbstract = self::isAbstract($codeSnifferFile, $functionPointer);

		$colonToken = $isAbstract
			? TokenHelper::findNextLocal($codeSnifferFile, T_COLON, $tokens[$functionPointer]['parenthesis_closer'] + 1)
			: TokenHelper::findNext($codeSnifferFile, T_COLON, $tokens[$functionPointer]['parenthesis_closer'] + 1, $tokens[$functionPointer]['scope_opener'] - 1);

		if ($colonToken === null) {
			return null;
		}

		$abstractExcludeTokens = array_merge(TokenHelper::$ineffectiveTokenCodes, [T_SEMICOLON]);

		$nullableToken = $isAbstract
			? TokenHelper::findNextLocalExcluding($codeSnifferFile, $abstractExcludeTokens, $colonToken + 1)
			: TokenHelper::findNextExcluding($codeSnifferFile, TokenHelper::$ineffectiveTokenCodes, $colonToken + 1, $tokens[$functionPointer]['scope_opener'] - 1);

		$nullable = $nullableToken !== null && $tokens[$nullableToken]['code'] === T_NULLABLE;

		$typeHint = '';
		$typeHintStartPointer = null;
		$typeHintEndPointer = null;
		$nextToken = $nullable ? $nullableToken : $colonToken;
		do {
			$nextToken = $isAbstract
				? TokenHelper::findNextLocalExcluding($codeSnifferFile, $abstractExcludeTokens, $nextToken + 1)
				: TokenHelper::findNextExcluding($codeSnifferFile, TokenHelper::$ineffectiveTokenCodes, $nextToken + 1, $tokens[$functionPointer]['scope_opener'] - 1);

			$isTypeHint = $nextToken !== null;
			if (!$isTypeHint) {
				break;
			}

			$typeHint .= $tokens[$nextToken]['content'];
			if ($typeHintStartPointer === null) {
				/** @var int $typeHintStartPointer */
				$typeHintStartPointer = $nextToken;
			}
			/** @var int $typeHintEndPointer */
			$typeHintEndPointer = $nextToken;
		} while ($isTypeHint);

		return $typeHint !== '' ? new ReturnTypeHint($typeHint, $nullable, $typeHintStartPointer, $typeHintEndPointer) : null;
	}

	public static function hasReturnTypeHint(File $codeSnifferFile, int $functionPointer): bool
	{
		return self::findReturnTypeHint($codeSnifferFile, $functionPointer) !== null;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $codeSnifferFile
	 * @param int $functionPointer
	 * @return \SlevomatCodingStandard\Helpers\Annotation[]
	 */
	public static function getParametersAnnotations(File $codeSnifferFile, int $functionPointer): array
	{
		return AnnotationHelper::getAnnotationsByName($codeSnifferFile, $functionPointer, '@param');
	}

	public static function findReturnAnnotation(File $codeSnifferFile, int $functionPointer): ?Annotation
	{
		$returnAnnotations = AnnotationHelper::getAnnotationsByName($codeSnifferFile, $functionPointer, '@return');

		if (count($returnAnnotations) === 0) {
			return null;
		}

		return $returnAnnotations[0];
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $codeSnifferFile
	 * @return string[]
	 */
	public static function getAllFunctionNames(File $codeSnifferFile): array
	{
		$previousFunctionPointer = 0;

		return array_map(
			function (int $functionPointer) use ($codeSnifferFile): string {
				return self::getName($codeSnifferFile, $functionPointer);
			},
			array_filter(
				iterator_to_array(self::getAllFunctionOrMethodPointers($codeSnifferFile, $previousFunctionPointer)),
				function (int $functionOrMethodPointer) use ($codeSnifferFile): bool {
					return !self::isMethod($codeSnifferFile, $functionOrMethodPointer);
				}
			)
		);
	}

	private static function getAllFunctionOrMethodPointers(File $codeSnifferFile, int &$previousFunctionPointer): Generator
	{
		do {
			$nextFunctionPointer = TokenHelper::findNext($codeSnifferFile, T_FUNCTION, $previousFunctionPointer + 1);
			if ($nextFunctionPointer === null) {
				break;
			}

			$previousFunctionPointer = $nextFunctionPointer;
			yield $nextFunctionPointer;
		} while (true);
	}

}
