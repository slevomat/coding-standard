<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use Exception;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\ConditionHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use const T_ELSE;
use const T_FALSE;
use const T_IF;
use const T_INLINE_ELSE;
use const T_INLINE_THEN;
use const T_RETURN;
use const T_SEMICOLON;
use const T_TRUE;
use function array_key_exists;
use function in_array;
use function sprintf;
use function strtolower;

class UselessConditionWithReturnSniff implements Sniff
{

	public const CODE_USELESS_IF_CONDITION = 'UselessIfCondition';
	public const CODE_USELESS_INLINE_IF_CONDITION = 'UselessInlineIfCondition';

	/**
	 * @return mixed[]
	 */
	public function register(): array
	{
		return [
			T_IF,
			T_INLINE_THEN,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $conditionPointer
	 */
	public function process(File $phpcsFile, $conditionPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$conditionPointer]['code'] === T_IF) {
			$this->processIf($phpcsFile, $conditionPointer);
		} else {
			$this->processInlineThen($phpcsFile, $conditionPointer);
		}
	}

	private function processIf(File $phpcsFile, int $ifPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		if (!array_key_exists('scope_closer', $tokens[$ifPointer])) {
			throw new Exception('"if" without curly braces is not supported.');
		}

		$ifBooleanPointer = $this->findBooleanAfterReturnInScope($phpcsFile, $tokens[$ifPointer]['scope_opener']);
		if ($ifBooleanPointer === null) {
			return;
		}

		$newCondition = function () use ($phpcsFile, $tokens, $ifBooleanPointer, $ifPointer): string {
			return strtolower($tokens[$ifBooleanPointer]['content']) === 'true'
				? TokenHelper::getContent($phpcsFile, $tokens[$ifPointer]['parenthesis_opener'] + 1, $tokens[$ifPointer]['parenthesis_closer'] - 1)
				: ConditionHelper::getNegativeCondition($phpcsFile, $tokens[$ifPointer]['parenthesis_opener'] + 1, $tokens[$ifPointer]['parenthesis_closer'] - 1);
		};

		$elsePointer = TokenHelper::findNextEffective($phpcsFile, $tokens[$ifPointer]['scope_closer'] + 1);

		if (
			$elsePointer !== null
			&& $tokens[$elsePointer]['code'] === T_ELSE
		) {
			if (!array_key_exists('scope_closer', $tokens[$elsePointer])) {
				throw new Exception('"else" without curly braces is not supported.');
			}

			$elseBooleanPointer = $this->findBooleanAfterReturnInScope($phpcsFile, $tokens[$elsePointer]['scope_opener']);
			if ($elseBooleanPointer === null) {
				return;
			}

			$fix = $phpcsFile->addFixableError('Useless condition.', $ifPointer, self::CODE_USELESS_IF_CONDITION);

			if (!$fix) {
				return;
			}

			$phpcsFile->fixer->beginChangeset();
			$phpcsFile->fixer->replaceToken($ifPointer, sprintf('return %s;', $newCondition()));
			for ($i = $ifPointer + 1; $i <= $tokens[$elsePointer]['scope_closer']; $i++) {
				$phpcsFile->fixer->replaceToken($i, '');
			}
			$phpcsFile->fixer->endChangeset();
		} else {
			/** @var int $returnPointer */
			$returnPointer = TokenHelper::findNextEffective($phpcsFile, $tokens[$ifPointer]['scope_closer'] + 1);
			if ($tokens[$returnPointer]['code'] !== T_RETURN) {
				return;
			}

			$semicolonPointer = $this->findSemicolonAfterReturnWithBoolean($phpcsFile, $returnPointer);
			if ($semicolonPointer === null) {
				return;
			}

			$fix = $phpcsFile->addFixableError('Useless condition.', $ifPointer, self::CODE_USELESS_IF_CONDITION);

			if (!$fix) {
				return;
			}

			$phpcsFile->fixer->beginChangeset();
			$phpcsFile->fixer->replaceToken($ifPointer, sprintf('return %s;', $newCondition()));
			for ($i = $ifPointer + 1; $i <= $semicolonPointer; $i++) {
				$phpcsFile->fixer->replaceToken($i, '');
			}
			$phpcsFile->fixer->endChangeset();
		}
	}

	private function processInlineThen(File $phpcsFile, int $inlineThenPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$returnPointer = TokenHelper::findPreviousLocal($phpcsFile, T_RETURN, $inlineThenPointer - 1);
		if ($returnPointer === null) {
			return;
		}

		$pointerAfterInlineThen = TokenHelper::findNextEffective($phpcsFile, $inlineThenPointer + 1);
		if ($tokens[$pointerAfterInlineThen]['code'] === T_INLINE_ELSE) {
			return;
		}

		if (!in_array($tokens[$pointerAfterInlineThen]['code'], [T_TRUE, T_FALSE], true)) {
			return;
		}

		$inlineElsePointer = TokenHelper::findNextEffective($phpcsFile, $pointerAfterInlineThen + 1);
		if ($tokens[$inlineElsePointer]['code'] !== T_INLINE_ELSE) {
			return;
		}

		$pointerAfterInlineElse = TokenHelper::findNextEffective($phpcsFile, $inlineElsePointer + 1);
		if (!in_array($tokens[$pointerAfterInlineElse]['code'], [T_TRUE, T_FALSE], true)) {
			return;
		}

		$semicolonPointer = TokenHelper::findNextEffective($phpcsFile, $pointerAfterInlineElse + 1);
		if ($tokens[$semicolonPointer]['code'] !== T_SEMICOLON) {
			return;
		}

		$fix = $phpcsFile->addFixableError('Useless condition.', $inlineThenPointer, self::CODE_USELESS_INLINE_IF_CONDITION);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		/** @var int $pointerAfterReturn */
		$pointerAfterReturn = TokenHelper::findNextEffective($phpcsFile, $returnPointer + 1);
		/** @var int $pointerBeforeInlineThen */
		$pointerBeforeInlineThen = TokenHelper::findPreviousEffective($phpcsFile, $inlineThenPointer - 1);

		if (strtolower($tokens[$pointerAfterInlineThen]['content']) === 'false') {
			$phpcsFile->fixer->replaceToken($pointerAfterReturn, ConditionHelper::getNegativeCondition($phpcsFile, $pointerAfterReturn, $pointerBeforeInlineThen));
			for ($i = $pointerAfterReturn + 1; $i <= $pointerBeforeInlineThen; $i++) {
				$phpcsFile->fixer->replaceToken($i, '');
			}
		}

		for ($i = $pointerBeforeInlineThen + 1; $i < $semicolonPointer; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}

		$phpcsFile->fixer->endChangeset();
	}

	private function findBooleanAfterReturnInScope(File $phpcsFile, int $scopeOpenerPointer): ?int
	{
		$tokens = $phpcsFile->getTokens();

		/** @var int $returnPointer */
		$returnPointer = TokenHelper::findNextEffective($phpcsFile, $scopeOpenerPointer + 1);
		if ($tokens[$returnPointer]['code'] !== T_RETURN) {
			return null;
		}

		$booleanPointer = $this->findBooleanAfterReturn($phpcsFile, $returnPointer);
		if ($booleanPointer === null) {
			return null;
		}

		$semicolonPointer = TokenHelper::findNextEffective($phpcsFile, $booleanPointer + 1);
		if ($tokens[$semicolonPointer]['code'] !== T_SEMICOLON) {
			return null;
		}

		return $booleanPointer;
	}

	private function findBooleanAfterReturn(File $phpcsFile, int $returnPointer): ?int
	{
		$tokens = $phpcsFile->getTokens();

		$booleanPointer = TokenHelper::findNextEffective($phpcsFile, $returnPointer + 1);
		if (in_array($tokens[$booleanPointer]['code'], [T_TRUE, T_FALSE], true)) {
			return $booleanPointer;
		}

		return null;
	}

	private function findSemicolonAfterReturnWithBoolean(File $phpcsFile, int $returnPointer): ?int
	{
		$tokens = $phpcsFile->getTokens();

		$booleanPointer = $this->findBooleanAfterReturn($phpcsFile, $returnPointer);
		if ($booleanPointer === null) {
			return null;
		}

		$semicolonPointer = TokenHelper::findNextEffective($phpcsFile, $booleanPointer + 1);
		if ($tokens[$semicolonPointer]['code'] !== T_SEMICOLON) {
			return null;
		}

		return $semicolonPointer;
	}

}
