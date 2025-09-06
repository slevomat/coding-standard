<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;
use function array_map;
use function in_array;
use const T_ANON_CLASS;
use const T_ATTRIBUTE;
use const T_ATTRIBUTE_END;
use const T_CLASS;
use const T_CLOSE_CURLY_BRACKET;
use const T_CLOSURE;
use const T_COMMA;
use const T_CONST;
use const T_ENUM;
use const T_ENUM_CASE;
use const T_FN;
use const T_FUNCTION;
use const T_INTERFACE;
use const T_OPEN_CURLY_BRACKET;
use const T_OPEN_PARENTHESIS;
use const T_SEMICOLON;
use const T_TRAIT;
use const T_VARIABLE;

/**
 * @internal
 */
class AttributeHelper
{

	private const ATTRIBUTE_TARGETS = [
		T_ANON_CLASS,
		T_CLASS,
		T_CLOSURE,
		T_CONST,
		T_ENUM,
		T_ENUM_CASE,
		T_FN,
		T_FUNCTION,
		T_INTERFACE,
		T_TRAIT,
		T_VARIABLE,
	];

	public static function hasAttribute(File $phpcsFile, int $pointer, string $attributeName): bool
	{
		$attributeNames = array_map(
			static fn (Attribute $name): string => $name->getFullyQualifiedName(),
			self::getAttributes($phpcsFile, $pointer),
		);

		return in_array($attributeName, $attributeNames, true);
	}

	/**
	 * @return list<Attribute>
	 */
	public static function getAttributes(File $phpcsFile, int $pointer): array
	{
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$pointer]['code'] !== T_ATTRIBUTE) {
			$attributeOpenerPointer = null;

			do {
				$attributeEndPointerCandidate = TokenHelper::findPrevious(
					$phpcsFile,
					[T_ATTRIBUTE_END, T_SEMICOLON, T_CLOSE_CURLY_BRACKET, T_OPEN_CURLY_BRACKET],
					$attributeOpenerPointer ?? $pointer - 1,
				);

				if (
					$attributeEndPointerCandidate === null
					|| $tokens[$attributeEndPointerCandidate]['code'] !== T_ATTRIBUTE_END
				) {
					break;
				}

				$attributeOpenerPointer = $tokens[$attributeEndPointerCandidate]['attribute_opener'];
			} while (true);

			if ($attributeOpenerPointer === null) {
				return [];
			}

		} else {
			$attributeOpenerPointer = $pointer;
		}

		$attributeCloserPointer = $tokens[$attributeOpenerPointer]['attribute_closer'];

		$actualPointer = $attributeOpenerPointer;
		$attributes = [];

		do {
			$attributeNameStartPointer = TokenHelper::findNextEffective($phpcsFile, $actualPointer + 1, $attributeCloserPointer);

			if ($attributeNameStartPointer === null) {
				break;
			}

			$attributeNameEndPointer = TokenHelper::findNextExcluding(
				$phpcsFile,
				TokenHelper::NAME_TOKEN_CODES,
				$attributeNameStartPointer + 1,
			) - 1;
			$attributeName = TokenHelper::getContent($phpcsFile, $attributeNameStartPointer, $attributeNameEndPointer);

			$pointerAfterAttributeName = TokenHelper::findNextEffective($phpcsFile, $attributeNameEndPointer + 1, $attributeCloserPointer);

			if ($pointerAfterAttributeName === null) {
				$attributes[] = new Attribute(
					$attributeOpenerPointer,
					$attributeName,
					NamespaceHelper::resolveClassName($phpcsFile, $attributeName, $attributeOpenerPointer),
					$attributeNameStartPointer,
					$attributeNameEndPointer,
				);
				break;
			}

			if ($tokens[$pointerAfterAttributeName]['code'] === T_COMMA) {
				$attributes[] = new Attribute(
					$attributeOpenerPointer,
					$attributeName,
					NamespaceHelper::resolveClassName($phpcsFile, $attributeName, $attributeOpenerPointer),
					$attributeNameStartPointer,
					$attributeNameEndPointer,
				);

				$actualPointer = $pointerAfterAttributeName;
			}

			if ($tokens[$pointerAfterAttributeName]['code'] === T_OPEN_PARENTHESIS) {
				$attributes[] = new Attribute(
					$attributeOpenerPointer,
					$attributeName,
					NamespaceHelper::resolveClassName($phpcsFile, $attributeName, $attributeOpenerPointer),
					$attributeNameStartPointer,
					$tokens[$pointerAfterAttributeName]['parenthesis_closer'],
					TokenHelper::getContent(
						$phpcsFile,
						$pointerAfterAttributeName,
						$tokens[$pointerAfterAttributeName]['parenthesis_closer'],
					),
				);

				$actualPointer = TokenHelper::findNextEffective(
					$phpcsFile,
					$tokens[$pointerAfterAttributeName]['parenthesis_closer'] + 1,
					$attributeCloserPointer,
				);

				continue;
			}

		} while ($actualPointer !== null);

		return $attributes;
	}

	/**
	 * Attributes have syntax that when defined incorrectly or in older PHP version, they are treated as comments.
	 * An example of incorrect declaration is variables that are not properties.
	 */
	public static function isValidAttribute(File $phpcsFile, int $attributeOpenerPointer): bool
	{
		return self::getAttributeTarget($phpcsFile, $attributeOpenerPointer) !== null;
	}

	public static function getAttributeTarget(File $phpcsFile, int $attributeOpenerPointer): ?int
	{
		$attributeTargetPointer = TokenHelper::findNext($phpcsFile, self::ATTRIBUTE_TARGETS, $attributeOpenerPointer);

		if ($attributeTargetPointer === null) {
			return null;
		}

		if (
			$phpcsFile->getTokens()[$attributeTargetPointer]['code'] === T_VARIABLE
			&& !PropertyHelper::isProperty($phpcsFile, $attributeTargetPointer)
			&& !ParameterHelper::isParameter($phpcsFile, $attributeTargetPointer)
		) {
			return null;
		}

		return $attributeTargetPointer;
	}

}
