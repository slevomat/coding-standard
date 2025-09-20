<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use function array_key_exists;
use function array_keys;
use function array_reverse;
use function array_values;
use function count;
use function in_array;
use function preg_match;
use function preg_replace;
use function sprintf;
use const T_ABSTRACT;
use const T_ANON_CLASS;
use const T_CLOSE_CURLY_BRACKET;
use const T_COMMA;
use const T_FINAL;
use const T_FUNCTION;
use const T_NULLABLE;
use const T_OPEN_CURLY_BRACKET;
use const T_OPEN_PARENTHESIS;
use const T_READONLY;
use const T_SEMICOLON;
use const T_STATIC;

/**
 * @internal
 */
class PropertyHelper
{

	public static function isProperty(File $phpcsFile, int $variablePointer, bool $promoted = false): bool
	{
		$tokens = $phpcsFile->getTokens();

		$previousPointer = TokenHelper::findPreviousExcluding(
			$phpcsFile,
			[...TokenHelper::INEFFECTIVE_TOKEN_CODES, ...TokenHelper::TYPE_HINT_TOKEN_CODES, T_NULLABLE],
			$variablePointer - 1,
		);

		if (in_array($tokens[$previousPointer]['code'], [T_FINAL, T_ABSTRACT], true)) {
			return true;
		}

		if ($tokens[$previousPointer]['code'] === T_STATIC) {
			$previousPointer = TokenHelper::findPreviousEffective($phpcsFile, $previousPointer - 1);
		}

		if (in_array(
			$tokens[$previousPointer]['code'],
			[...array_values(Tokens::SCOPE_MODIFIERS), T_READONLY],
			true,
		)) {
			$constructorPointer = TokenHelper::findPrevious($phpcsFile, T_FUNCTION, $previousPointer - 1);

			if ($constructorPointer === null) {
				return true;
			}

			return $tokens[$constructorPointer]['parenthesis_closer'] < $previousPointer || $promoted;
		}

		if (
			!array_key_exists('conditions', $tokens[$variablePointer])
			|| count($tokens[$variablePointer]['conditions']) === 0
		) {
			return false;
		}

		$functionPointer = TokenHelper::findPrevious(
			$phpcsFile,
			[...TokenHelper::FUNCTION_TOKEN_CODES, T_SEMICOLON, T_CLOSE_CURLY_BRACKET, T_OPEN_CURLY_BRACKET],
			$variablePointer - 1,
		);
		if (
			$functionPointer !== null
			&& in_array($tokens[$functionPointer]['code'], TokenHelper::FUNCTION_TOKEN_CODES, true)
		) {
			return false;
		}

		$previousParenthesisPointer = TokenHelper::findPrevious($phpcsFile, T_OPEN_PARENTHESIS, $variablePointer - 1);
		if ($previousParenthesisPointer !== null && $tokens[$previousParenthesisPointer]['parenthesis_closer'] > $variablePointer) {
			$previousPointer = TokenHelper::findPreviousEffective($phpcsFile, $previousParenthesisPointer - 1);
			if ($previousPointer !== null && in_array($tokens[$previousPointer]['content'], ['get', 'set'], true)) {
				// Parameter of property hook
				return false;
			}
		}

		$previousCurlyBracketPointer = TokenHelper::findPrevious($phpcsFile, T_OPEN_CURLY_BRACKET, $variablePointer - 1);
		if (
			$previousCurlyBracketPointer !== null
			&& $tokens[$previousCurlyBracketPointer]['bracket_closer'] > $variablePointer
		) {
			// Variable in content of property hook
			if (!array_key_exists('scope_condition', $tokens[$previousCurlyBracketPointer])) {
				return false;
			}
		}

		$conditionCode = array_values($tokens[$variablePointer]['conditions'])[count($tokens[$variablePointer]['conditions']) - 1];

		return in_array($conditionCode, Tokens::OO_SCOPE_TOKENS, true);
	}

	public static function getStartPointer(File $phpcsFile, int $propertyPointer): int
	{
		$previousCodeEndPointer = TokenHelper::findPrevious(
			$phpcsFile,
			[
				// Previous property or constant
				T_SEMICOLON,
				// Previous method or property with hooks
				T_CLOSE_CURLY_BRACKET,
				// Start of the class
				T_OPEN_CURLY_BRACKET,
				// Start of the constructor
				T_OPEN_PARENTHESIS,
				// Previous parameter in the constructor
				T_COMMA,
			],
			$propertyPointer - 1,
		);

		$startPointer = TokenHelper::findPreviousEffective($phpcsFile, $propertyPointer - 1, $previousCodeEndPointer);

		do {
			$possibleStartPointer = TokenHelper::findPrevious(
				$phpcsFile,
				TokenHelper::PROPERTY_MODIFIERS_TOKEN_CODES,
				$startPointer - 1,
				$previousCodeEndPointer,
			);

			if ($possibleStartPointer === null) {
				return $startPointer;
			}

			$startPointer = $possibleStartPointer;
		} while (true);
	}

	public static function getEndPointer(File $phpcsFile, int $propertyPointer): int
	{
		$tokens = $phpcsFile->getTokens();

		$endPointer = TokenHelper::findNext($phpcsFile, [T_SEMICOLON, T_OPEN_CURLY_BRACKET], $propertyPointer + 1);

		return $tokens[$endPointer]['code'] === T_OPEN_CURLY_BRACKET
			? $tokens[$endPointer]['bracket_closer']
			: $endPointer;
	}

	public static function findTypeHint(File $phpcsFile, int $propertyPointer): ?TypeHint
	{
		$tokens = $phpcsFile->getTokens();

		$propertyStartPointer = self::getStartPointer($phpcsFile, $propertyPointer);

		$typeHintEndPointer = TokenHelper::findPrevious(
			$phpcsFile,
			TokenHelper::TYPE_HINT_TOKEN_CODES,
			$propertyPointer - 1,
			$propertyStartPointer,
		);
		if ($typeHintEndPointer === null) {
			return null;
		}

		$typeHintStartPointer = TypeHintHelper::getStartPointer($phpcsFile, $typeHintEndPointer);

		$previousPointer = TokenHelper::findPreviousEffective($phpcsFile, $typeHintStartPointer - 1, $propertyStartPointer);
		$nullable = $previousPointer !== null && $tokens[$previousPointer]['code'] === T_NULLABLE;

		if ($nullable) {
			$typeHintStartPointer = $previousPointer;
		}

		$typeHint = TokenHelper::getContent($phpcsFile, $typeHintStartPointer, $typeHintEndPointer);

		if (!$nullable) {
			$nullable = preg_match('~(?:^|\|\s*)null(?:\s*\||$)~i', $typeHint) === 1;
		}

		/** @var string $typeHint */
		$typeHint = preg_replace('~\s+~', '', $typeHint);

		return new TypeHint($typeHint, $nullable, $typeHintStartPointer, $typeHintEndPointer);
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
