<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;
use const T_CLOSE_CURLY_BRACKET;
use const T_CLOSE_SQUARE_BRACKET;
use const T_DOLLAR;
use const T_DOUBLE_COLON;
use const T_NS_SEPARATOR;
use const T_OBJECT_OPERATOR;
use const T_OPEN_CURLY_BRACKET;
use const T_OPEN_SQUARE_BRACKET;
use const T_PARENT;
use const T_SELF;
use const T_STATIC;
use const T_STRING;
use const T_VARIABLE;
use function in_array;

class VariableHelper
{

	public static function getVariableContent(File $phpcsFile, int $startPointer, int $endPointer): string
	{
		$tokens = $phpcsFile->getTokens();

		$variableContent = '';
		for ($i = $startPointer; $i <= $endPointer; $i++) {
			if (in_array($tokens[$i]['code'], TokenHelper::$ineffectiveTokenCodes, true)) {
				continue;
			}

			$variableContent .= $tokens[$i]['content'];
		}

		return $variableContent;
	}

	public static function findVariableStartPointer(File $phpcsFile, int $endPointer): ?int
	{
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$endPointer]['code'] === T_STRING) {
			/** @var int $previousPointer */
			$previousPointer = TokenHelper::findPreviousEffective($phpcsFile, $endPointer - 1);
			if (in_array($tokens[$previousPointer]['code'], [T_OBJECT_OPERATOR, T_DOUBLE_COLON], true)) {
				return self::getVariableStartPointerBeforeOperator($phpcsFile, $previousPointer);
			}

			return $endPointer;
		}

		if (in_array($tokens[$endPointer]['code'], [T_CLOSE_CURLY_BRACKET, T_CLOSE_SQUARE_BRACKET], true)) {
			return self::getVariableStartPointerBeforeVariablePart($phpcsFile, $tokens[$endPointer]['bracket_opener']);
		}

		if ($tokens[$endPointer]['code'] === T_VARIABLE) {
			return self::getVariableStartPointerBeforeVariablePart($phpcsFile, $endPointer);
		}

		return null;
	}

	private static function getVariableStartPointerBeforeOperator(File $phpcsFile, int $operatorPointer): int
	{
		$tokens = $phpcsFile->getTokens();

		/** @var int $previousPointer */
		$previousPointer = TokenHelper::findPreviousEffective($phpcsFile, $operatorPointer - 1);

		if ($tokens[$previousPointer]['code'] === T_STRING) {
			$previousPointer = TokenHelper::findPreviousExcluding($phpcsFile, TokenHelper::$nameTokenCodes, $previousPointer - 1) + 1;
		}

		if (
			$tokens[$operatorPointer]['code'] === T_DOUBLE_COLON
			&& in_array($tokens[$previousPointer]['code'], [T_NS_SEPARATOR, T_STRING, T_SELF, T_STATIC, T_PARENT], true)
		) {
			return $previousPointer;
		}

		if ($tokens[$previousPointer]['code'] === T_STRING) {
			/** @var int $possibleOperatorPointer */
			$possibleOperatorPointer = TokenHelper::findPreviousEffective($phpcsFile, $previousPointer - 1);
			if ($tokens[$possibleOperatorPointer]['code'] === T_OBJECT_OPERATOR) {
				return self::getVariableStartPointerBeforeOperator($phpcsFile, $possibleOperatorPointer);
			}
		}

		if (in_array($tokens[$previousPointer]['code'], [T_CLOSE_CURLY_BRACKET, T_CLOSE_SQUARE_BRACKET], true)) {
			return self::getVariableStartPointerBeforeVariablePart($phpcsFile, $tokens[$previousPointer]['bracket_opener']);
		}

		return self::getVariableStartPointerBeforeVariablePart($phpcsFile, $previousPointer);
	}

	private static function getVariableStartPointerBeforeVariablePart(File $phpcsFile, int $variablePartPointer): int
	{
		$tokens = $phpcsFile->getTokens();

		/** @var int $previousPointer */
		$previousPointer = TokenHelper::findPreviousEffective($phpcsFile, $variablePartPointer - 1);

		if ($tokens[$previousPointer]['code'] === T_DOLLAR) {
			/** @var int $previousPointer */
			$previousPointer = TokenHelper::findPreviousEffective($phpcsFile, $previousPointer - 1);
		}

		if (in_array($tokens[$previousPointer]['code'], [T_OBJECT_OPERATOR, T_DOUBLE_COLON], true)) {
			return self::getVariableStartPointerBeforeOperator($phpcsFile, $previousPointer);
		}

		if (in_array($tokens[$previousPointer]['code'], [T_CLOSE_CURLY_BRACKET, T_CLOSE_SQUARE_BRACKET], true)) {
			return self::getVariableStartPointerBeforeVariablePart($phpcsFile, $tokens[$previousPointer]['bracket_opener']);
		}

		if ($tokens[$previousPointer]['code'] === T_VARIABLE) {
			return self::getVariableStartPointerBeforeVariablePart($phpcsFile, $previousPointer);
		}

		return $variablePartPointer;
	}

	public static function findVariableEndPointer(File $phpcsFile, int $startPointer): ?int
	{
		$tokens = $phpcsFile->getTokens();

		if (in_array($tokens[$startPointer]['code'], TokenHelper::$nameTokenCodes, true)) {
			$startPointer = TokenHelper::findNextExcluding($phpcsFile, TokenHelper::$nameTokenCodes, $startPointer + 1) - 1;
		} elseif ($tokens[$startPointer]['code'] === T_DOLLAR) {
			$startPointer = TokenHelper::findNextEffective($phpcsFile, $startPointer + 1);
		}

		/** @var int $nextPointer */
		$nextPointer = TokenHelper::findNextEffective($phpcsFile, $startPointer + 1);

		if (
			in_array($tokens[$startPointer]['code'], [T_STRING, T_SELF, T_STATIC, T_PARENT], true)
			&& $tokens[$nextPointer]['code'] === T_DOUBLE_COLON
		) {
			return self::getVariableEndPointerAfterOperator($phpcsFile, $nextPointer);
		}

		if ($tokens[$startPointer]['code'] === T_VARIABLE) {
			if (in_array($tokens[$nextPointer]['code'], [T_DOUBLE_COLON, T_OBJECT_OPERATOR], true)) {
				return self::getVariableEndPointerAfterOperator($phpcsFile, $nextPointer);
			}

			return $startPointer;
		}

		return null;
	}

	private static function getVariableEndPointerAfterOperator(File $phpcsFile, int $operatorPointer): int
	{
		$tokens = $phpcsFile->getTokens();

		/** @var int $nextPointer */
		$nextPointer = TokenHelper::findNextEffective($phpcsFile, $operatorPointer + 1);

		if ($tokens[$nextPointer]['code'] === T_DOLLAR) {
			/** @var int $nextPointer */
			$nextPointer = TokenHelper::findNextEffective($phpcsFile, $nextPointer + 1);
		}

		if ($tokens[$nextPointer]['code'] === T_OPEN_CURLY_BRACKET) {
			return self::getVariableEndPointerAfterVariablePart($phpcsFile, $tokens[$nextPointer]['bracket_closer']);
		}

		return self::getVariableEndPointerAfterVariablePart($phpcsFile, $nextPointer);
	}

	private static function getVariableEndPointerAfterVariablePart(File $phpcsFile, int $variablePartPointer): int
	{
		$tokens = $phpcsFile->getTokens();

		/** @var int $nextPointer */
		$nextPointer = TokenHelper::findNextEffective($phpcsFile, $variablePartPointer + 1);

		if (in_array($tokens[$nextPointer]['code'], [T_OBJECT_OPERATOR, T_DOUBLE_COLON], true)) {
			return self::getVariableEndPointerAfterOperator($phpcsFile, $nextPointer);
		}

		if ($tokens[$nextPointer]['code'] === T_OPEN_SQUARE_BRACKET) {
			return self::getVariableEndPointerAfterVariablePart($phpcsFile, $tokens[$nextPointer]['bracket_closer']);
		}

		return $variablePartPointer;
	}

}
