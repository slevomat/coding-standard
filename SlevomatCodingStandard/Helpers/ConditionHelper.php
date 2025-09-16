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
use const T_OPEN_PARENTHESIS;
use const T_PARENT;
use const T_SELF;
use const T_STATIC;
use const T_STRING;
use const T_VARIABLE;

/**
 * @internal
 */
class ConditionHelper
{

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
					[T_OPEN_PARENTHESIS, T_LESS_THAN, T_GREATER_THAN],
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

			return true;

		} while (true);

		return false;
	}

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
		$booleanPointers = TokenHelper::findNextAll(
			$phpcsFile,
			Tokens::BOOLEAN_OPERATORS,
			$conditionBoundaryStartPointer,
			$conditionBoundaryEndPointer + 1,
		);

		if ($tokens[$pointerAfterConditionStart]['code'] === T_BOOLEAN_NOT) {
			$pointerAfterBooleanNot = TokenHelper::findNextEffective($phpcsFile, $pointerAfterConditionStart + 1);
			if ($tokens[$pointerAfterBooleanNot]['code'] === T_OPEN_PARENTHESIS) {
				if ($nested && $booleanPointers !== []) {
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

		if (count($booleanPointers) > 0) {
			return self::getNegativeLogicalCondition($phpcsFile, $conditionBoundaryStartPointer, $conditionBoundaryEndPointer);
		}

		if ($tokens[$pointerAfterConditionStart]['code'] === T_BOOLEAN_NOT) {
			return self::removeBooleanNot($condition);
		}

		if (TokenHelper::findNext(
			$phpcsFile,
			[T_INSTANCEOF, T_BITWISE_AND, T_COALESCE, T_INLINE_THEN],
			$conditionBoundaryStartPointer,
			$conditionBoundaryEndPointer + 1,
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

		if (in_array($tokens[$pointerAfterConditionStart]['code'], [T_VARIABLE, T_SELF, T_STATIC, T_PARENT], true)) {
			$identificatorEndPointer = IdentificatorHelper::findEndPointer($phpcsFile, $pointerAfterConditionStart);
			$pointerAfterIdentificatorEnd = TokenHelper::findNextEffective($phpcsFile, $identificatorEndPointer + 1);
			if (
				$tokens[$pointerAfterIdentificatorEnd]['code'] === T_OPEN_PARENTHESIS
				&& $tokens[$pointerAfterIdentificatorEnd]['parenthesis_closer'] === $conditionBoundaryEndPointer
			) {
				return sprintf('!%s', $condition);
			}
		}

		$comparisonPointer = TokenHelper::findNext(
			$phpcsFile,
			[T_IS_EQUAL, T_IS_NOT_EQUAL, T_IS_IDENTICAL, T_IS_NOT_IDENTICAL, T_IS_SMALLER_OR_EQUAL, T_IS_GREATER_OR_EQUAL, T_LESS_THAN, T_GREATER_THAN],
			$conditionBoundaryStartPointer,
			$conditionBoundaryEndPointer + 1,
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
				if ($tokens[$i]['code'] === T_OPEN_PARENTHESIS) {
					$negativeCondition .= TokenHelper::getContent($phpcsFile, $i, $tokens[$i]['parenthesis_closer']);
					$i = $tokens[$i]['parenthesis_closer'];
					continue;
				}

				$negativeCondition .= array_key_exists($tokens[$i]['code'], $comparisonReplacements)
					? $comparisonReplacements[$tokens[$i]['code']]
					: $tokens[$i]['content'];
			}

			return $negativeCondition;
		}

		return sprintf('!%s', $condition);
	}

	private static function removeBooleanNot(string $condition): string
	{
		return preg_replace('~^!\\s*~', '', $condition);
	}

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
				array_merge([T_OPEN_PARENTHESIS, T_CLOSE_PARENTHESIS], Tokens::BOOLEAN_OPERATORS),
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

}
