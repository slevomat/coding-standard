<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Arrays;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\ArrayHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function in_array;

class MultiLineArrayEndBracketPlacementSniff implements Sniff
{

	public const CODE_ARRAY_END_WRONG_PLACEMENT = 'ArrayEndWrongPlacement';

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return TokenHelper::$arrayTokenCodes;
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $stackPointer
	 */
	public function process(File $phpcsFile, $stackPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		if (ArrayHelper::isMultiLine($phpcsFile, $stackPointer) === false) {
			return;
		}

		[$arrayOpenerPointer, $arrayCloserPointer] = ArrayHelper::openClosePointers($tokens[$stackPointer]);

		$nextEffective = TokenHelper::findNextEffective($phpcsFile, $arrayOpenerPointer + 1, $arrayCloserPointer);
		if ($nextEffective === null || in_array($tokens[$nextEffective]['code'], TokenHelper::$arrayTokenCodes, true) === false) {
			return;
		}

		[$nextPointerOpener, $nextPointerCloser] = ArrayHelper::openClosePointers($tokens[$nextEffective]);

		$arraysStartAtSameLine = $tokens[$arrayOpenerPointer]['line'] === $tokens[$nextPointerOpener]['line'];
		$arraysEndAtSameLine = $tokens[$arrayCloserPointer]['line'] === $tokens[$nextPointerCloser]['line'];
		if (!$arraysStartAtSameLine || $arraysEndAtSameLine) {
			return;
		}

		$error = "Expected nested array to end at the same line as it's parent. Either put the nested array's end at the same line as the parent's end, or put the nested array start on it's own line.";
		$fix = $phpcsFile->addFixableError($error, $arrayOpenerPointer, self::CODE_ARRAY_END_WRONG_PLACEMENT);
		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->addContent($arrayOpenerPointer, $phpcsFile->eolChar);
	}

}
