<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use InvalidArgumentException;
use PHP_CodeSniffer\Files\File;
use function sprintf;
use const T_ATTRIBUTE;
use const T_CLASS;
use const T_CLOSURE;
use const T_COMMA;
use const T_CONST;
use const T_ENUM;
use const T_ENUM_CASE;
use const T_FN;
use const T_FUNCTION;
use const T_INTERFACE;
use const T_OPEN_PARENTHESIS;
use const T_STRING;
use const T_TRAIT;
use const T_VARIABLE;

/**
 * @internal
 */
class AttributeHelper
{

	private const ATTRIBUTABLE_TOKENS = [
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

	/**
	 * @return list<int>
	 */
	public static function getAttributesPointersInsideAttributeTags(File $phpcsFile, int $attributeOpenPointer): array
	{
		$attributes = [];

		$tokens = $phpcsFile->getTokens();

		if ($tokens[$attributeOpenPointer]['code'] !== T_ATTRIBUTE) {
			throw new InvalidArgumentException(
				sprintf('Token %d must be attribute, %s given.', $attributeOpenPointer, $tokens[$attributeOpenPointer]['type'])
			);
		}

		$attributeEndPointer = $tokens[$attributeOpenPointer]['attribute_closer'];

		$startPointer = $attributeOpenPointer;
		/** @var int|null $commaPointer */
		$commaPointer = 0;

		$attributeNamePointer = TokenHelper::findNext($phpcsFile, T_STRING, $startPointer + 1, $attributeEndPointer);
		$attributes[] = $attributeNamePointer;

		while ($commaPointer !== null) {
			$parenthesisOpenPointer = TokenHelper::findNext($phpcsFile, T_OPEN_PARENTHESIS, $startPointer + 1, $attributeEndPointer);
			$commaPointer = TokenHelper::findNext($phpcsFile, T_COMMA, $startPointer + 1, $attributeEndPointer);

			if ($commaPointer === null) {
				// No more attributes
				break;
			}

			// We have comma, lets see if it's before parenthesis
			if ($parenthesisOpenPointer === null || $commaPointer < $parenthesisOpenPointer) {
				// Yes, it's before, so we have an attribute
				$startPointer = $commaPointer;
				$attributeNamePointer = TokenHelper::findNext($phpcsFile, T_STRING, $startPointer + 1, $attributeEndPointer);
				$attributes[] = $attributeNamePointer;
			} else {
				// No, it's after, but we do not know if inside parenthesis or outside
				$startPointer = $tokens[$parenthesisOpenPointer]['parenthesis_closer'];
			}
		}

		return $attributes;
	}

	/**
	 * Attributes have syntax that when defined incorrectly or in older PHP version, they are treated as comments
	 * An example of incorrect declaration is variables that are not properties
	 */
	public static function isValidAttribute(File $phpcsFile, int $attributeOpenPointer): bool
	{
		$attributedTokenPointer = TokenHelper::findNext($phpcsFile, self::ATTRIBUTABLE_TOKENS, $attributeOpenPointer);

		if ($attributedTokenPointer === null) {
			return false;
		}

		$tokens = $phpcsFile->getTokens();

		return
			$tokens[$attributedTokenPointer]['code'] !== T_VARIABLE
			|| PropertyHelper::isProperty($phpcsFile, $attributedTokenPointer)
			|| ParameterHelper::isParameter($phpcsFile, $attributedTokenPointer);
	}

}
