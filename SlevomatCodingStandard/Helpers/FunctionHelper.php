<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class FunctionHelper
{

	/**
	 * @param \PHP_CodeSniffer_File $codeSnifferFile
	 * @param integer $functionPointer
	 * @return string
	 */
	public static function getName(\PHP_CodeSniffer_File $codeSnifferFile, $functionPointer)
	{
		$tokens = $codeSnifferFile->getTokens();
		return $tokens[$codeSnifferFile->findNext(T_STRING, $functionPointer + 1, $tokens[$functionPointer]['parenthesis_opener'])]['content'];
	}

	/**
	 * @param \PHP_CodeSniffer_File $codeSnifferFile
	 * @param integer $functionPointer
	 * @return string
	 */
	public static function getFullyQualifiedName(\PHP_CodeSniffer_File $codeSnifferFile, $functionPointer)
	{
		$name = self::getName($codeSnifferFile, $functionPointer);
		$namespace = NamespaceHelper::findCurrentNamespaceName($codeSnifferFile, $functionPointer);

		if (self::isMethod($codeSnifferFile, $functionPointer)) {
			foreach (array_reverse($codeSnifferFile->getTokens()[$functionPointer]['conditions'], true) as $conditionPointer => $conditionTokenCode) {
				if ($conditionTokenCode === T_ANON_CLASS) {
					return sprintf('class@anonymous::%s', $name);
				} elseif (in_array($conditionTokenCode, [T_CLASS, T_INTERFACE, T_TRAIT], true)) {
					$name = sprintf('%s%s::%s', NamespaceHelper::NAMESPACE_SEPARATOR, ClassHelper::getName($codeSnifferFile, $conditionPointer), $name);
					break;
				}
			}

			return $namespace !== null ? sprintf('%s%s%s', NamespaceHelper::NAMESPACE_SEPARATOR, $namespace, $name) : $name;
		} else {
			return $namespace !== null ? sprintf('%s%s%s%s', NamespaceHelper::NAMESPACE_SEPARATOR, $namespace, NamespaceHelper::NAMESPACE_SEPARATOR, $name) : $name;
		}
	}

	/**
	 * @param \PHP_CodeSniffer_File $codeSnifferFile
	 * @param integer $functionPointer
	 * @return boolean
	 */
	public static function isAbstract(\PHP_CodeSniffer_File $codeSnifferFile, $functionPointer)
	{
		return !isset($codeSnifferFile->getTokens()[$functionPointer]['scope_opener']);
	}

	/**
	 * @param \PHP_CodeSniffer_File $codeSnifferFile
	 * @param integer $functionPointer
	 * @return boolean
	 */
	public static function isMethod(\PHP_CodeSniffer_File $codeSnifferFile, $functionPointer)
	{
		foreach (array_reverse($codeSnifferFile->getTokens()[$functionPointer]['conditions']) as $conditionTokenCode) {
			if (in_array($conditionTokenCode, [T_CLASS, T_INTERFACE, T_TRAIT, T_ANON_CLASS], true)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param \PHP_CodeSniffer_File $codeSnifferFile
	 * @param integer $functionPointer
	 * @return string[]
	 */
	public static function getParametersWithoutTypeHint(\PHP_CodeSniffer_File $codeSnifferFile, $functionPointer)
	{
		return array_keys(array_filter(self::getParametersTypeHints($codeSnifferFile, $functionPointer), function ($parameterTypeHint) {
			return $parameterTypeHint === null;
		}));
	}

	/**
	 * @param \PHP_CodeSniffer_File $codeSnifferFile
	 * @param integer $functionPointer
	 * @return string[]
	 */
	public static function getParametersTypeHints(\PHP_CodeSniffer_File $codeSnifferFile, $functionPointer)
	{
		$tokens = $codeSnifferFile->getTokens();

		$parametersTypeHints = [];
		for ($i = $tokens[$functionPointer]['parenthesis_opener'] + 1; $i < $tokens[$functionPointer]['parenthesis_closer']; $i++) {
			if ($tokens[$i]['code'] === T_VARIABLE) {
				$parameterName = $tokens[$i]['content'];
				$parameterTypeHint = null;

				$previousToken = $i;
				do {
					$previousToken = TokenHelper::findPreviousExcluding($codeSnifferFile, [T_WHITESPACE, T_COMMENT, T_BITWISE_AND, T_ELLIPSIS], $previousToken - 1, $tokens[$functionPointer]['parenthesis_opener'] + 1);
					$isTypeHint = $previousToken !== null && $tokens[$previousToken]['code'] !== T_COMMA;
					if ($isTypeHint) {
						$parameterTypeHint = $tokens[$previousToken]['content'] . $parameterTypeHint;
					}
				} while ($isTypeHint);

				$parametersTypeHints[$parameterName] = $parameterTypeHint;
			}
		}

		return $parametersTypeHints;
	}

	/**
	 * @param \PHP_CodeSniffer_File $codeSnifferFile
	 * @param integer $functionPointer
	 * @return boolean
	 */
	public static function returnsValue(\PHP_CodeSniffer_File $codeSnifferFile, $functionPointer)
	{
		$tokens = $codeSnifferFile->getTokens();

		for ($i = $tokens[$functionPointer]['scope_opener'] + 1; $i < $tokens[$functionPointer]['scope_closer']; $i++) {
			if ($tokens[$i]['code'] === T_RETURN && $tokens[TokenHelper::findNextEffective($codeSnifferFile, $i + 1)]['code'] !== T_SEMICOLON) {
				foreach (array_reverse($tokens[$i]['conditions'], true) as $conditionPointer => $conditionTokenCode) {
					if ($conditionTokenCode === T_CLOSURE || $conditionTokenCode === T_ANON_CLASS) {
						continue 2;
					} elseif ($conditionPointer === $functionPointer) {
						return true;
					}
				}
			}
		}

		return false;
	}

	/**
	 * @param \PHP_CodeSniffer_File $codeSnifferFile
	 * @param integer $functionPointer
	 * @return string|null
	 */
	public static function findReturnTypeHint(\PHP_CodeSniffer_File $codeSnifferFile, $functionPointer)
	{
		$tokens = $codeSnifferFile->getTokens();

		$isAbstract = self::isAbstract($codeSnifferFile, $functionPointer);

		$colonToken = $isAbstract
			? $codeSnifferFile->findNext(T_COLON, $tokens[$functionPointer]['parenthesis_closer'] + 1, null, false, null, true)
			: $codeSnifferFile->findNext(T_COLON, $tokens[$functionPointer]['parenthesis_closer'] + 1, $tokens[$functionPointer]['scope_opener'] - 1);

		if ($colonToken === false) {
			return null;
		}

		$returnTypeHint = null;
		$nextToken = $colonToken;
		do {
			$nextToken = $isAbstract
				? $codeSnifferFile->findNext([T_WHITESPACE, T_COMMENT, T_SEMICOLON], $nextToken + 1, null, true, null, true)
				: $codeSnifferFile->findNext([T_WHITESPACE, T_COMMENT], $nextToken + 1, $tokens[$functionPointer]['scope_opener'] - 1, true);

			$isTypeHint = $nextToken !== false;
			if ($isTypeHint) {
				$returnTypeHint .= $tokens[$nextToken]['content'];
			}
		} while ($isTypeHint);

		return $returnTypeHint;
	}

	/**
	 * @param \PHP_CodeSniffer_File $codeSnifferFile
	 * @param integer $functionPointer
	 * @return boolean
	 */
	public static function hasReturnTypeHint(\PHP_CodeSniffer_File $codeSnifferFile, $functionPointer)
	{
		return self::findReturnTypeHint($codeSnifferFile, $functionPointer) !== null;
	}

	/**
	 * @param \PHP_CodeSniffer_File $codeSnifferFile
	 * @param integer $functionPointer
	 * @return string[]
	 */
	public static function getParametersAnnotations(\PHP_CodeSniffer_File $codeSnifferFile, $functionPointer)
	{
		return AnnotationHelper::getAnnotationsByName($codeSnifferFile, $functionPointer, '@param');
	}

	/**
	 * @param \PHP_CodeSniffer_File $codeSnifferFile
	 * @param integer $functionPointer
	 * @return string|null
	 */
	public static function findReturnAnnotation(\PHP_CodeSniffer_File $codeSnifferFile, $functionPointer)
	{
		$returnAnnotations = AnnotationHelper::getAnnotationsByName($codeSnifferFile, $functionPointer, '@return');

		if (count($returnAnnotations) === 0) {
			return null;
		}

		return $returnAnnotations[0];
	}

}
