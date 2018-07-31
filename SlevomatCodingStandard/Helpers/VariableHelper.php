<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;
use const T_DOLLAR;
use const T_DOUBLE_COLON;
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

	public static function findVariableEndPointer(File $phpcsFile, int $startPointer): ?int
	{
		$tokens = $phpcsFile->getTokens();

		if (in_array($tokens[$startPointer]['code'], TokenHelper::$nameTokenCodes, true)) {
			$startPointer = TokenHelper::findNextExcluding($phpcsFile, TokenHelper::$nameTokenCodes, $startPointer + 1) - 1;
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
