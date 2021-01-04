<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\TernaryOperatorHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function in_array;
use function ltrim;
use function sprintf;
use function trim;
use const T_BOOLEAN_NOT;
use const T_CASE;
use const T_COMMA;
use const T_DOUBLE_ARROW;
use const T_EQUAL;
use const T_INLINE_ELSE;
use const T_INLINE_THEN;
use const T_OPEN_PARENTHESIS;
use const T_OPEN_SHORT_ARRAY;
use const T_OPEN_SQUARE_BRACKET;
use const T_OPEN_TAG;
use const T_OPEN_TAG_WITH_ECHO;
use const T_RETURN;
use const T_THROW;

class RequireShortTernaryOperatorSniff implements Sniff
{

	public const CODE_REQUIRED_SHORT_TERNARY_OPERATOR = 'RequiredShortTernaryOperator';

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_INLINE_THEN,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param File $phpcsFile
	 * @param int $inlineThenPointer
	 */
	public function process(File $phpcsFile, $inlineThenPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$nextPointer = TokenHelper::findNextEffective($phpcsFile, $inlineThenPointer + 1);

		if ($tokens[$nextPointer]['code'] === T_INLINE_ELSE) {
			return;
		}

		$inlineElsePointer = TernaryOperatorHelper::getElsePointer($phpcsFile, $inlineThenPointer);
		$inlineElseEndPointer = TernaryOperatorHelper::getEndPointer($phpcsFile, $inlineThenPointer, $inlineElsePointer);

		$thenContent = trim(TokenHelper::getContent($phpcsFile, $inlineThenPointer + 1, $inlineElsePointer - 1));
		$elseContent = trim(TokenHelper::getContent($phpcsFile, $inlineElsePointer + 1, $inlineElseEndPointer));

		$pointerBeforeCondition = $inlineThenPointer;
		do {
			$pointerBeforeCondition = TokenHelper::findPrevious(
				$phpcsFile,
				[T_EQUAL, T_DOUBLE_ARROW, T_COMMA, T_RETURN, T_THROW, T_CASE, T_OPEN_TAG, T_OPEN_TAG_WITH_ECHO, T_OPEN_SQUARE_BRACKET, T_OPEN_SHORT_ARRAY, T_OPEN_PARENTHESIS],
				$pointerBeforeCondition - 1
			);

			if (
				in_array($tokens[$pointerBeforeCondition]['code'], [T_OPEN_SQUARE_BRACKET, T_OPEN_SHORT_ARRAY], true)
				&& $tokens[$pointerBeforeCondition]['bracket_closer'] < $inlineThenPointer
			) {
				continue;
			}

			if (
				$tokens[$pointerBeforeCondition]['code'] === T_OPEN_PARENTHESIS
				&& $tokens[$pointerBeforeCondition]['parenthesis_closer'] < $inlineThenPointer
			) {
				continue;
			}

			break;

		} while (true);

		/** @var int $conditionStartPointer */
		$conditionStartPointer = TokenHelper::findNextEffective($phpcsFile, $pointerBeforeCondition + 1);
		$conditionEndPointer = TokenHelper::findPreviousEffective($phpcsFile, $inlineThenPointer - 1);

		$condition = TokenHelper::getContent($phpcsFile, $conditionStartPointer, $conditionEndPointer);

		if ($tokens[$conditionStartPointer]['code'] === T_BOOLEAN_NOT) {
			if ($elseContent !== ltrim($condition, '!')) {
				return;
			}
		} else {
			if ($thenContent !== $condition) {
				return;
			}
		}

		$fix = $phpcsFile->addFixableError('Use short ternary operator.', $inlineThenPointer, self::CODE_REQUIRED_SHORT_TERNARY_OPERATOR);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		if ($tokens[$conditionStartPointer]['code'] === T_BOOLEAN_NOT) {
			$phpcsFile->fixer->replaceToken($conditionStartPointer, '');

			for ($i = $inlineThenPointer + 1; $i <= $inlineElseEndPointer; $i++) {
				$phpcsFile->fixer->replaceToken($i, '');
			}

			$phpcsFile->fixer->addContent($inlineThenPointer, sprintf(': %s', $thenContent));

		} else {
			for ($i = $inlineThenPointer + 1; $i < $inlineElsePointer; $i++) {
				$phpcsFile->fixer->replaceToken($i, '');
			}
		}

		$phpcsFile->fixer->endChangeset();
	}

}
