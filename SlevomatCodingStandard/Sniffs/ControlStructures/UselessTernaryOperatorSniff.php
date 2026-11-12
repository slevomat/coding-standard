<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\ConditionHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\ScopeHelper;
use SlevomatCodingStandard\Helpers\TernaryOperatorHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function in_array;
use const T_CLOSE_TAG;
use const T_DOUBLE_ARROW;
use const T_FALSE;
use const T_INLINE_ELSE;
use const T_INLINE_THEN;
use const T_LOGICAL_AND;
use const T_LOGICAL_OR;
use const T_LOGICAL_XOR;
use const T_MATCH_ARROW;
use const T_TRUE;
use const T_YIELD;
use const T_YIELD_FROM;

class UselessTernaryOperatorSniff implements Sniff
{

	public const CODE_USELESS_TERNARY_OPERATOR = 'UselessTernaryOperator';

	public bool $assumeAllConditionExpressionsAreAlreadyBoolean = false;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_INLINE_THEN,
		];
	}

	public function process(File $phpcsFile, int $inlineThenPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$pointerAfterInlineThen = TokenHelper::findNextEffective($phpcsFile, $inlineThenPointer + 1);
		if ($tokens[$pointerAfterInlineThen]['code'] === T_INLINE_ELSE) {
			$inlineElsePointer = $pointerAfterInlineThen;
		} else {
			if (!in_array($tokens[$pointerAfterInlineThen]['code'], [T_TRUE, T_FALSE], true)) {
				return;
			}

			$inlineElsePointer = TokenHelper::findNextEffective($phpcsFile, $pointerAfterInlineThen + 1);
			if ($tokens[$inlineElsePointer]['code'] !== T_INLINE_ELSE) {
				return;
			}
		}

		$pointerAfterInlineElse = TokenHelper::findNextEffective($phpcsFile, $inlineElsePointer + 1);
		if (!in_array($tokens[$pointerAfterInlineElse]['code'], [T_TRUE, T_FALSE], true)) {
			return;
		}

		$conditionStartPointer = TernaryOperatorHelper::getStartPointer($phpcsFile, $inlineThenPointer);
		/** @var int $conditionEndPointer */
		$conditionEndPointer = TokenHelper::findPreviousEffective($phpcsFile, $inlineThenPointer - 1);

		$errorParameters = [
			'Useless ternary operator.',
			$inlineThenPointer,
			self::CODE_USELESS_TERNARY_OPERATOR,
		];

		if (
			!$this->assumeAllConditionExpressionsAreAlreadyBoolean
			&& !ConditionHelper::conditionReturnsBoolean($phpcsFile, $conditionStartPointer, $conditionEndPointer)
		) {
			if ($tokens[$pointerAfterInlineThen]['code'] !== T_INLINE_ELSE) {
				$phpcsFile->addError(...$errorParameters);
			}
			return;
		}

		$inlineElseEndPointer = TernaryOperatorHelper::getEndPointer($phpcsFile, $inlineThenPointer, $inlineElsePointer);

		if ($this->containsYieldInSameScope($phpcsFile, $inlineThenPointer, $inlineElsePointer, $inlineElseEndPointer)) {
			// Removing the only "yield" would change the generator status of the function
			$phpcsFile->addError(...$errorParameters);
			return;
		}

		$fix = $phpcsFile->addFixableError(...$errorParameters);

		if (!$fix) {
			return;
		}

		$pointerAfterTernaryOperator = TokenHelper::findNextEffective($phpcsFile, $inlineElseEndPointer + 1);

		$phpcsFile->fixer->beginChangeset();

		if ($tokens[$pointerAfterInlineThen]['code'] === T_FALSE) {
			$negativeCondition = ConditionHelper::getNegativeCondition($phpcsFile, $conditionStartPointer, $conditionEndPointer);

			FixerHelper::change($phpcsFile, $conditionStartPointer, $conditionEndPointer, $negativeCondition);
		}

		FixerHelper::removeBetween($phpcsFile, $conditionEndPointer, $pointerAfterTernaryOperator);

		if (in_array(
			$tokens[$pointerAfterTernaryOperator]['code'],
			[T_LOGICAL_AND, T_LOGICAL_OR, T_LOGICAL_XOR, T_INLINE_THEN, T_CLOSE_TAG, T_DOUBLE_ARROW, T_MATCH_ARROW],
			true,
		)) {
			FixerHelper::addBefore($phpcsFile, $pointerAfterTernaryOperator, ' ');
		}

		$phpcsFile->fixer->endChangeset();
	}

	/**
	 * Determines whether the ternary false-branch expression contains a yield or yield from
	 * within the same function scope, which would change the function's generator status if removed.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param int $inlineThenPointer The token pointer to the T_INLINE_THEN ("?") token.
	 * @param int $startPointer The token pointer after which to start searching (typically T_INLINE_ELSE).
	 * @param int $endPointer The token pointer up to which to search (the end of the ternary expression).
	 * @return bool True if a yield token exists in the same scope, false otherwise.
	 */
	private function containsYieldInSameScope(File $phpcsFile, int $inlineThenPointer, int $startPointer, int $endPointer): bool
	{
		foreach (TokenHelper::findNextAll($phpcsFile, [T_YIELD, T_YIELD_FROM], $startPointer + 1, $endPointer + 1) as $yieldPointer) {
			if (ScopeHelper::isInSameScope($phpcsFile, $inlineThenPointer, $yieldPointer)) {
				return true;
			}
		}

		return false;
	}

}
