<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use function array_key_exists;
use function array_merge;
use function count;
use function in_array;
use function preg_replace;
use function sprintf;
use function strtolower;
use function trim;
use const T_BITWISE_AND;
use const T_BOOLEAN_AND;
use const T_BOOLEAN_NOT;
use const T_BOOLEAN_OR;
use const T_CLOSE_PARENTHESIS;
use const T_COALESCE;
use const T_GREATER_THAN;
use const T_INLINE_THEN;
use const T_INSTANCEOF;
use const T_IS_EQUAL;
use const T_IS_GREATER_OR_EQUAL;
use const T_IS_IDENTICAL;
use const T_IS_NOT_EQUAL;
use const T_IS_NOT_IDENTICAL;
use const T_IS_SMALLER_OR_EQUAL;
use const T_LESS_THAN;
use const T_LOGICAL_AND;
use const T_LOGICAL_OR;
use const T_LOGICAL_XOR;
use const T_OPEN_CURLY_BRACKET;
use const T_OPEN_PARENTHESIS;
use const T_OPEN_SHORT_ARRAY;
use const T_OPEN_SQUARE_BRACKET;
use const T_STRING;
use const T_VARIABLE;

/**
 * @internal
 */
class ConditionHelper
{

	/**
	 * Determines whether the condition expression is guaranteed to return a boolean value based on
	 * top-level boolean or comparison operators, ignoring comparisons enclosed within parentheses,
	 * brackets, or curly braces (such as match expressions, closures, or arrays).
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param int $conditionBoundaryStartPointer The token pointer to the start of the condition.
	 * @param int $conditionBoundaryEndPointer The token pointer to the end of the condition.
	 * @return bool True if the expression evaluates to a boolean, false otherwise.
	 */
	public static function conditionReturnsBoolean(
		File $phpcsFile,
		int $conditionBoundaryStartPointer,
		int $conditionBoundaryEndPointer
	): bool
	{
		$tokens = $phpcsFile->getTokens();

		$conditionContent = strtolower(
			trim(TokenHelper::getContent($phpcsFile, $conditionBoundaryStartPointer, $conditionBoundaryEndPointer)),
		);

		if ($conditionContent === 'false' || $conditionContent === 'true') {
			return true;
		}

		$actualPointer = $conditionBoundaryStartPointer;

		do {
			$actualPointer = TokenHelper::findNext(
				$phpcsFile,
				array_merge(
					[
						T_OPEN_PARENTHESIS,
						T_OPEN_SHORT_ARRAY,
						T_OPEN_SQUARE_BRACKET,
						T_OPEN_CURLY_BRACKET,
						T_LESS_THAN,
						T_GREATER_THAN,
					],
					Tokens::BOOLEAN_OPERATORS,
					Tokens::EQUALITY_TOKENS,
				),
				$actualPointer,
				$conditionBoundaryEndPointer + 1,
			);

			if ($actualPointer === null) {
				break;
			}

			if ($tokens[$actualPointer]['code'] === T_OPEN_PARENTHESIS) {
				$actualPointer = $tokens[$actualPointer]['parenthesis_closer'];
				continue;
			}

			if (in_array($tokens[$actualPointer]['code'], [T_OPEN_SHORT_ARRAY, T_OPEN_SQUARE_BRACKET], true)) {
				$actualPointer = $tokens[$actualPointer]['bracket_closer'];
				continue;
			}

			if ($tokens[$actualPointer]['code'] === T_OPEN_CURLY_BRACKET) {
				$closer = $tokens[$actualPointer]['bracket_closer'] ?? $tokens[$actualPointer]['scope_closer'] ?? null;
				if ($closer !== null) {
					$actualPointer = $closer;
					continue;
				}
			}

			return true;

		} while (true);

		return false;
	}

	/**
	 * Computes the logically inverted (negative) representation of a given condition expression range,
	 * handling outer parentheses unwrapping and delegating to logical or atomic negation helpers.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param int $conditionBoundaryStartPointer The token pointer where the condition begins.
	 * @param int $conditionBoundaryEndPointer The token pointer where the condition ends.
	 * @param bool $nested Whether the condition is a sub-expression of an enclosing logical expression.
	 * @return string The negated condition expression string.
	 */
	public static function getNegativeCondition(
		File $phpcsFile,
		int $conditionBoundaryStartPointer,
		int $conditionBoundaryEndPointer,
		bool $nested = false
	): string
	{
		/** @var int $conditionStartPointer */
		$conditionStartPointer = TokenHelper::findNextEffective($phpcsFile, $conditionBoundaryStartPointer);
		/** @var int $conditionEndPointer */
		$conditionEndPointer = TokenHelper::findPreviousEffective($phpcsFile, $conditionBoundaryEndPointer);

		$tokens = $phpcsFile->getTokens();
		if (
			$tokens[$conditionStartPointer]['code'] === T_OPEN_PARENTHESIS
			&& $tokens[$conditionStartPointer]['parenthesis_closer'] === $conditionEndPointer
		) {
			/** @var int $conditionStartPointer */
			$conditionStartPointer = TokenHelper::findNextEffective($phpcsFile, $conditionStartPointer + 1);
			/** @var int $conditionEndPointer */
			$conditionEndPointer = TokenHelper::findPreviousEffective($phpcsFile, $conditionEndPointer - 1);
		}

		return sprintf(
			'%s%s%s',
			$conditionBoundaryStartPointer !== $conditionStartPointer
				? TokenHelper::getContent(
					$phpcsFile,
					$conditionBoundaryStartPointer,
					$conditionStartPointer - 1,
				)
				: '',
			self::getNegativeConditionPart($phpcsFile, $conditionStartPointer, $conditionEndPointer, $nested),
			$conditionBoundaryEndPointer !== $conditionEndPointer
				? TokenHelper::getContent(
					$phpcsFile,
					$conditionEndPointer + 1,
					$conditionBoundaryEndPointer,
				)
				: '',
		);
	}

	/**
	 * Computes the negative representation of an individual condition operand or atomic expression,
	 * inverting comparisons, stripping leading negations, or prefixing with "!".
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param int $conditionBoundaryStartPointer The token pointer where the condition part begins.
	 * @param int $conditionBoundaryEndPointer The token pointer where the condition part ends.
	 * @param bool $nested Whether the condition is a sub-expression of an enclosing logical expression.
	 * @return string The negated condition part string.
	 */
	private static function getNegativeConditionPart(
		File $phpcsFile,
		int $conditionBoundaryStartPointer,
		int $conditionBoundaryEndPointer,
		bool $nested
	): string
	{
		$tokens = $phpcsFile->getTokens();

		$condition = TokenHelper::getContent($phpcsFile, $conditionBoundaryStartPointer, $conditionBoundaryEndPointer);

		if (strtolower($condition) === 'true') {
			return 'false';
		}
		if (strtolower($condition) === 'false') {
			return 'true';
		}

		$pointerAfterConditionStart = TokenHelper::findNextEffective($phpcsFile, $conditionBoundaryStartPointer);
		$topLevelBooleanPointers = self::findTopLevelAll(
			$phpcsFile,
			Tokens::BOOLEAN_OPERATORS,
			$conditionBoundaryStartPointer,
			$conditionBoundaryEndPointer,
		);

		if ($tokens[$pointerAfterConditionStart]['code'] === T_BOOLEAN_NOT) {
			$pointerAfterBooleanNot = TokenHelper::findNextEffective($phpcsFile, $pointerAfterConditionStart + 1);
			if ($tokens[$pointerAfterBooleanNot]['code'] === T_OPEN_PARENTHESIS) {
				if (
					$nested
					&& TokenHelper::findNext(
						$phpcsFile,
						Tokens::BOOLEAN_OPERATORS,
						$conditionBoundaryStartPointer,
						$conditionBoundaryEndPointer + 1,
					) !== null
				) {
					return self::removeBooleanNot($condition);
				}

				$pointerAfterParenthesisCloser = TokenHelper::findNextEffective(
					$phpcsFile,
					$tokens[$pointerAfterBooleanNot]['parenthesis_closer'] + 1,
					$conditionBoundaryEndPointer + 1,
				);
				if (
					$pointerAfterParenthesisCloser === null
					|| $pointerAfterParenthesisCloser === $conditionBoundaryEndPointer
				) {
					return TokenHelper::getContent(
						$phpcsFile,
						$pointerAfterBooleanNot + 1,
						$tokens[$pointerAfterBooleanNot]['parenthesis_closer'] - 1,
					);
				}
			}
		}

		if (count($topLevelBooleanPointers) > 0) {
			return self::getNegativeLogicalCondition($phpcsFile, $conditionBoundaryStartPointer, $conditionBoundaryEndPointer);
		}

		if ($tokens[$pointerAfterConditionStart]['code'] === T_BOOLEAN_NOT) {
			return self::removeBooleanNot($condition);
		}

		if (self::findTopLevel(
			$phpcsFile,
			[T_INSTANCEOF, T_BITWISE_AND, T_COALESCE, T_INLINE_THEN],
			$conditionBoundaryStartPointer,
			$conditionBoundaryEndPointer,
		) !== null) {
			return sprintf('!(%s)', $condition);
		}

		if ($tokens[$pointerAfterConditionStart]['code'] === T_STRING) {
			$pointerAfterConditionStart = TokenHelper::findNextEffective($phpcsFile, $pointerAfterConditionStart + 1);
			if (
				$tokens[$pointerAfterConditionStart]['code'] === T_OPEN_PARENTHESIS
				&& $tokens[$pointerAfterConditionStart]['parenthesis_closer'] === $conditionBoundaryEndPointer
			) {
				return sprintf('!%s', $condition);
			}
		}

		if (in_array($tokens[$pointerAfterConditionStart]['code'], [T_VARIABLE, ...TokenHelper::CLASS_KEYWORD_CODES], true)) {
			$identificatorEndPointer = IdentificatorHelper::findEndPointer($phpcsFile, $pointerAfterConditionStart);
			$pointerAfterIdentificatorEnd = TokenHelper::findNextEffective($phpcsFile, $identificatorEndPointer + 1);
			if (
				$tokens[$pointerAfterIdentificatorEnd]['code'] === T_OPEN_PARENTHESIS
				&& $tokens[$pointerAfterIdentificatorEnd]['parenthesis_closer'] === $conditionBoundaryEndPointer
			) {
				return sprintf('!%s', $condition);
			}
		}

		$comparisonPointer = self::findTopLevel(
			$phpcsFile,
			[T_IS_EQUAL, T_IS_NOT_EQUAL, T_IS_IDENTICAL, T_IS_NOT_IDENTICAL, T_IS_SMALLER_OR_EQUAL, T_IS_GREATER_OR_EQUAL, T_LESS_THAN, T_GREATER_THAN],
			$conditionBoundaryStartPointer,
			$conditionBoundaryEndPointer,
		);
		if ($comparisonPointer !== null) {
			$comparisonReplacements = [
				T_IS_EQUAL => '!=',
				T_IS_NOT_EQUAL => '==',
				T_IS_IDENTICAL => '!==',
				T_IS_NOT_IDENTICAL => '===',
				T_IS_GREATER_OR_EQUAL => '<',
				T_IS_SMALLER_OR_EQUAL => '>',
				T_GREATER_THAN => '<=',
				T_LESS_THAN => '>=',
			];

			$negativeCondition = '';
			for ($i = $conditionBoundaryStartPointer; $i <= $conditionBoundaryEndPointer; $i++) {
				// Skip calls()
				if ($tokens[$i]['code'] === T_OPEN_PARENTHESIS && isset($tokens[$i]['parenthesis_closer'])) {
					$negativeCondition .= TokenHelper::getContent($phpcsFile, $i, $tokens[$i]['parenthesis_closer']);
					$i = $tokens[$i]['parenthesis_closer'];
					continue;
				}

				if (
					in_array($tokens[$i]['code'], [T_OPEN_SHORT_ARRAY, T_OPEN_SQUARE_BRACKET], true)
					&& isset($tokens[$i]['bracket_closer'])
				) {
					$negativeCondition .= TokenHelper::getContent($phpcsFile, $i, $tokens[$i]['bracket_closer']);
					$i = $tokens[$i]['bracket_closer'];
					continue;
				}

				if ($tokens[$i]['code'] === T_OPEN_CURLY_BRACKET) {
					$closer = $tokens[$i]['bracket_closer'] ?? $tokens[$i]['scope_closer'] ?? null;
					if ($closer !== null) {
						$negativeCondition .= TokenHelper::getContent($phpcsFile, $i, $closer);
						$i = $closer;
						continue;
					}
				}

				$negativeCondition .= array_key_exists($tokens[$i]['code'], $comparisonReplacements)
					? $comparisonReplacements[$tokens[$i]['code']]
					: $tokens[$i]['content'];
			}

			return $negativeCondition;
		}

		return sprintf('!%s', $condition);
	}

	/**
	 * Removes the leading boolean not ("!") operator and any following whitespace from a condition string.
	 *
	 * @param string $condition The condition string starting with "!".
	 * @return string The condition string without the leading negation.
	 */
	private static function removeBooleanNot(string $condition): string
	{
		return preg_replace('~^!\\s*~', '', $condition);
	}

	/**
	 * Computes the negative representation of a logical compound condition expression by applying De Morgan's laws
	 * to top-level boolean operators and recursively negating each operand.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param int $conditionBoundaryStartPointer The token pointer where the logical condition begins.
	 * @param int $conditionBoundaryEndPointer The token pointer where the logical condition ends.
	 * @return string The negated logical condition expression string.
	 */
	private static function getNegativeLogicalCondition(
		File $phpcsFile,
		int $conditionBoundaryStartPointer,
		int $conditionBoundaryEndPointer
	): string
	{
		if (TokenHelper::findNext($phpcsFile, T_LOGICAL_XOR, $conditionBoundaryStartPointer, $conditionBoundaryEndPointer) !== null) {
			return sprintf('!(%s)', TokenHelper::getContent($phpcsFile, $conditionBoundaryStartPointer, $conditionBoundaryEndPointer));
		}

		$tokens = $phpcsFile->getTokens();

		$booleanOperatorReplacements = [
			T_BOOLEAN_AND => '||',
			T_BOOLEAN_OR => '&&',
			T_LOGICAL_AND => 'or',
			T_LOGICAL_OR => 'and',
		];

		$negativeCondition = '';

		$nestedConditionStartPointer = $conditionBoundaryStartPointer;
		$actualPointer = $conditionBoundaryStartPointer;
		$parenthesesLevel = 0;

		$operatorsOnLevel = [];

		do {
			$actualPointer = TokenHelper::findNext(
				$phpcsFile,
				array_merge(
					[
						T_OPEN_PARENTHESIS,
						T_CLOSE_PARENTHESIS,
						T_OPEN_SHORT_ARRAY,
						T_OPEN_SQUARE_BRACKET,
						T_OPEN_CURLY_BRACKET,
					],
					Tokens::BOOLEAN_OPERATORS,
				),
				$actualPointer,
				$conditionBoundaryEndPointer + 1,
			);

			if ($actualPointer === null) {
				break;
			}

			if ($tokens[$actualPointer]['code'] === T_OPEN_PARENTHESIS) {
				$pointerBeforeParenthesisOpener = TokenHelper::findPreviousEffective($phpcsFile, $actualPointer - 1);
				if ($tokens[$pointerBeforeParenthesisOpener]['code'] === T_STRING) {
					$actualPointer = $tokens[$actualPointer]['parenthesis_closer'] + 1;
					continue;
				}

				$parenthesesLevel++;
				$actualPointer++;
				continue;
			}

			if ($tokens[$actualPointer]['code'] === T_CLOSE_PARENTHESIS) {
				$parenthesesLevel--;
				$actualPointer++;
				continue;
			}

			if (
				in_array($tokens[$actualPointer]['code'], [T_OPEN_SHORT_ARRAY, T_OPEN_SQUARE_BRACKET], true)
				&& isset($tokens[$actualPointer]['bracket_closer'])
			) {
				$actualPointer = $tokens[$actualPointer]['bracket_closer'] + 1;
				continue;
			}

			if ($tokens[$actualPointer]['code'] === T_OPEN_CURLY_BRACKET) {
				$closer = $tokens[$actualPointer]['bracket_closer'] ?? $tokens[$actualPointer]['scope_closer'] ?? null;
				if ($closer !== null) {
					$actualPointer = $closer + 1;
					continue;
				}
			}

			if ($parenthesesLevel !== 0) {
				$actualPointer++;
				continue;
			}

			if (
				array_key_exists($parenthesesLevel, $operatorsOnLevel)
				&& $operatorsOnLevel[$parenthesesLevel] !== $tokens[$actualPointer]['code']
			) {
				return sprintf('!(%s)', TokenHelper::getContent($phpcsFile, $conditionBoundaryStartPointer, $conditionBoundaryEndPointer));
			}

			$operatorsOnLevel[$parenthesesLevel] = $tokens[$actualPointer]['code'];

			$negativeCondition .= self::getNegativeCondition($phpcsFile, $nestedConditionStartPointer, $actualPointer - 1, true);
			$negativeCondition .= $booleanOperatorReplacements[$tokens[$actualPointer]['code']];

			$nestedConditionStartPointer = $actualPointer + 1;
			$actualPointer++;

		} while (true);

		return $negativeCondition . self::getNegativeCondition(
			$phpcsFile,
			$nestedConditionStartPointer,
			$conditionBoundaryEndPointer,
			true,
		);
	}

	/**
	 * Locates the first occurrence of any of the specified token types that is at the top level
	 * of the expression, skipping tokens enclosed in parentheses, brackets, or curly braces.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param array<int|string, int|string> $types The token codes to search for.
	 * @param int $startPointer The token pointer where the search begins.
	 * @param int $endPointer The token pointer where the search ends (inclusive).
	 * @return int|null The pointer to the first matching top-level token, or null if none found.
	 */
	private static function findTopLevel(File $phpcsFile, array $types, int $startPointer, int $endPointer): ?int
	{
		$tokens = $phpcsFile->getTokens();

		$searchTypes = array_merge(
			[
				T_OPEN_PARENTHESIS,
				T_OPEN_SHORT_ARRAY,
				T_OPEN_SQUARE_BRACKET,
				T_OPEN_CURLY_BRACKET,
			],
			$types,
		);

		$actualPointer = $startPointer;
		do {
			$actualPointer = TokenHelper::findNext($phpcsFile, $searchTypes, $actualPointer, $endPointer + 1);
			if ($actualPointer === null) {
				return null;
			}

			if ($tokens[$actualPointer]['code'] === T_OPEN_PARENTHESIS && isset($tokens[$actualPointer]['parenthesis_closer'])) {
				$actualPointer = $tokens[$actualPointer]['parenthesis_closer'] + 1;
				continue;
			}

			if (
				in_array($tokens[$actualPointer]['code'], [T_OPEN_SHORT_ARRAY, T_OPEN_SQUARE_BRACKET], true)
				&& isset($tokens[$actualPointer]['bracket_closer'])
			) {
				$actualPointer = $tokens[$actualPointer]['bracket_closer'] + 1;
				continue;
			}

			if ($tokens[$actualPointer]['code'] === T_OPEN_CURLY_BRACKET) {
				$closer = $tokens[$actualPointer]['bracket_closer'] ?? $tokens[$actualPointer]['scope_closer'] ?? null;
				if ($closer !== null) {
					$actualPointer = $closer + 1;
					continue;
				}
			}

			if (in_array($tokens[$actualPointer]['code'], $types, true)) {
				return $actualPointer;
			}

			$actualPointer++;
		} while ($actualPointer <= $endPointer);

		return null;
	}

	/**
	 * Locates all occurrences of the specified token types that are at the top level
	 * of the expression, skipping tokens enclosed in parentheses, brackets, or curly braces.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param array<int|string, int|string> $types The token codes to search for.
	 * @param int $startPointer The token pointer where the search begins.
	 * @param int $endPointer The token pointer where the search ends (inclusive).
	 * @return list<int> Pointers to all matching top-level tokens.
	 */
	private static function findTopLevelAll(File $phpcsFile, array $types, int $startPointer, int $endPointer): array
	{
		$tokens = $phpcsFile->getTokens();

		$searchTypes = array_merge(
			[
				T_OPEN_PARENTHESIS,
				T_OPEN_SHORT_ARRAY,
				T_OPEN_SQUARE_BRACKET,
				T_OPEN_CURLY_BRACKET,
			],
			$types,
		);

		$pointers = [];
		$actualPointer = $startPointer;
		do {
			$actualPointer = TokenHelper::findNext($phpcsFile, $searchTypes, $actualPointer, $endPointer + 1);
			if ($actualPointer === null) {
				break;
			}

			if ($tokens[$actualPointer]['code'] === T_OPEN_PARENTHESIS && isset($tokens[$actualPointer]['parenthesis_closer'])) {
				$actualPointer = $tokens[$actualPointer]['parenthesis_closer'] + 1;
				continue;
			}

			if (
				in_array($tokens[$actualPointer]['code'], [T_OPEN_SHORT_ARRAY, T_OPEN_SQUARE_BRACKET], true)
				&& isset($tokens[$actualPointer]['bracket_closer'])
			) {
				$actualPointer = $tokens[$actualPointer]['bracket_closer'] + 1;
				continue;
			}

			if ($tokens[$actualPointer]['code'] === T_OPEN_CURLY_BRACKET) {
				$closer = $tokens[$actualPointer]['bracket_closer'] ?? $tokens[$actualPointer]['scope_closer'] ?? null;
				if ($closer !== null) {
					$actualPointer = $closer + 1;
					continue;
				}
			}

			if (in_array($tokens[$actualPointer]['code'], $types, true)) {
				$pointers[] = $actualPointer;
			}

			$actualPointer++;
		} while ($actualPointer <= $endPointer);

		return $pointers;
	}

}
