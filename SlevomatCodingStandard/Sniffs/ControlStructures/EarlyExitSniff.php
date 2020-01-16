<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use Exception;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\ConditionHelper;
use SlevomatCodingStandard\Helpers\IndentationHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use Throwable;
use function array_key_exists;
use function in_array;
use function sort;
use function sprintf;
use const T_CLOSE_CURLY_BRACKET;
use const T_CLOSURE;
use const T_COLON;
use const T_DO;
use const T_ELSE;
use const T_ELSEIF;
use const T_FOR;
use const T_FOREACH;
use const T_FUNCTION;
use const T_IF;
use const T_OPEN_CURLY_BRACKET;
use const T_SEMICOLON;
use const T_WHILE;
use const T_WHITESPACE;
use const T_YIELD;

class EarlyExitSniff implements Sniff
{

	public const CODE_EARLY_EXIT_NOT_USED = 'EarlyExitNotUsed';
	public const CODE_USELESS_ELSEIF = 'UselessElseIf';
	public const CODE_USELESS_ELSE = 'UselessElse';

	/** @var bool */
	public $ignoreStandaloneIfInScope = false;

	/**
	 * @return (int|string)[]
	 */
	public function register(): array
	{
		return [
			T_IF,
			T_ELSEIF,
			T_ELSE,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $pointer
	 */
	public function process(File $phpcsFile, $pointer): void
	{
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$pointer]['code'] === T_IF) {
			$this->processIf($phpcsFile, $pointer);
		} elseif ($tokens[$pointer]['code'] === T_ELSEIF) {
			$this->processElseIf($phpcsFile, $pointer);
		} else {
			$this->processElse($phpcsFile, $pointer);
		}
	}

	private function processElse(File $phpcsFile, int $elsePointer): void
	{
		$tokens = $phpcsFile->getTokens();

		if (!array_key_exists('scope_opener', $tokens[$elsePointer])) {
			// Else without curly braces is not supported.
			return;
		}

		try {
			$allConditionsPointers = $this->getAllConditionsPointers($phpcsFile, $elsePointer);
		} catch (Throwable $e) {
			// Else without curly braces is not supported.
			return;
		}

		$ifPointer = $allConditionsPointers[0];
		$ifEarlyExitPointer = null;
		$elseEarlyExitPointer = null;
		$previousConditionPointer = null;
		$previousConditionEarlyExitPointer = null;

		foreach ($allConditionsPointers as $conditionPointer) {
			$conditionEarlyExitPointer = $this->findEarlyExitInScope($phpcsFile, $tokens[$conditionPointer]['scope_opener'], $tokens[$conditionPointer]['scope_closer']);

			if ($conditionPointer === $elsePointer) {
				$elseEarlyExitPointer = $conditionEarlyExitPointer;
				continue;
			}

			$previousConditionPointer = $conditionPointer;
			$previousConditionEarlyExitPointer = $conditionEarlyExitPointer;

			if ($conditionPointer === $ifPointer) {
				$ifEarlyExitPointer = $conditionEarlyExitPointer;
				continue;
			}

			if ($conditionEarlyExitPointer === null) {
				return;
			}
		}

		if ($ifEarlyExitPointer === null && $elseEarlyExitPointer === null) {
			return;
		}

		if ($elseEarlyExitPointer !== null && $previousConditionEarlyExitPointer === null) {
			$fix = $phpcsFile->addFixableError(
				'Use early exit instead of else.',
				$elsePointer,
				self::CODE_EARLY_EXIT_NOT_USED
			);

			if (!$fix) {
				return;
			}

			$phpcsFile->fixer->beginChangeset();

			for ($i = $ifPointer; $i <= $tokens[$elsePointer]['scope_closer']; $i++) {
				$phpcsFile->fixer->replaceToken($i, '');
			}

			$ifCode = $this->getScopeCode($phpcsFile, $ifPointer);
			$elseCode = $this->getScopeCode($phpcsFile, $elsePointer);
			$negativeIfCondition = ConditionHelper::getNegativeCondition($phpcsFile, $tokens[$ifPointer]['parenthesis_opener'], $tokens[$ifPointer]['parenthesis_closer']);
			$afterIfCode = IndentationHelper::fixIndentation($ifCode, $phpcsFile->eolChar, IndentationHelper::getIndentation($phpcsFile, $ifPointer));

			$phpcsFile->fixer->addContent(
				$ifPointer,
				sprintf(
					'if %s {%s}%s%s',
					$negativeIfCondition,
					$elseCode,
					$phpcsFile->eolChar,
					$afterIfCode
				)
			);

			$phpcsFile->fixer->endChangeset();

			return;
		}

		if (
			$previousConditionEarlyExitPointer !== null
			&& $tokens[$previousConditionEarlyExitPointer]['code'] === T_YIELD
			&& $tokens[$elseEarlyExitPointer]['code'] === T_YIELD
		) {
			return;
		}

		$pointerAfterElseCondition = TokenHelper::findNextEffective($phpcsFile, $tokens[$elsePointer]['scope_closer'] + 1);
		if ($pointerAfterElseCondition === null || $tokens[$pointerAfterElseCondition]['code'] !== T_CLOSE_CURLY_BRACKET) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'Remove useless else to reduce code nesting.',
			$elsePointer,
			self::CODE_USELESS_ELSE
		);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		for ($i = $tokens[$previousConditionPointer]['scope_closer'] + 1; $i <= $tokens[$elsePointer]['scope_closer']; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}

		$elseCode = $this->getScopeCode($phpcsFile, $elsePointer);
		$afterIfCode = IndentationHelper::fixIndentation($elseCode, $phpcsFile->eolChar, IndentationHelper::addIndentation(IndentationHelper::getIndentation($phpcsFile, $previousConditionPointer)));

		$phpcsFile->fixer->addContent(
			$tokens[$elsePointer]['scope_closer'],
			sprintf(
				'%s%s',
				$phpcsFile->eolChar,
				$afterIfCode
			)
		);

		$phpcsFile->fixer->endChangeset();
	}

	private function processElseIf(File $phpcsFile, int $elseIfPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		try {
			$allConditionsPointers = $this->getAllConditionsPointers($phpcsFile, $elseIfPointer);
		} catch (Throwable $e) {
			// Elseif without curly braces is not supported.
			return;
		}

		$elseIfEarlyExitPointer = null;
		$previousConditionEarlyExitPointer = null;

		foreach ($allConditionsPointers as $conditionPointer) {
			$conditionEarlyExitPointer = $this->findEarlyExitInScope($phpcsFile, $tokens[$conditionPointer]['scope_opener'], $tokens[$conditionPointer]['scope_closer']);

			if ($conditionPointer === $elseIfPointer) {
				$elseIfEarlyExitPointer = $conditionEarlyExitPointer;
				break;
			}

			$previousConditionEarlyExitPointer = $conditionEarlyExitPointer;

			if ($conditionEarlyExitPointer === null) {
				return;
			}
		}

		if (
			$previousConditionEarlyExitPointer !== null
			&& $tokens[$previousConditionEarlyExitPointer]['code'] === T_YIELD
			&& $elseIfEarlyExitPointer !== null
			&& $tokens[$elseIfEarlyExitPointer]['code'] === T_YIELD
		) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'Use if instead of elseif.',
			$elseIfPointer,
			self::CODE_USELESS_ELSEIF
		);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		/** @var int $pointerBeforeElseIfPointer */
		$pointerBeforeElseIfPointer = TokenHelper::findPreviousExcluding($phpcsFile, T_WHITESPACE, $elseIfPointer - 1);

		for ($i = $pointerBeforeElseIfPointer + 1; $i < $elseIfPointer; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}

		$phpcsFile->fixer->addNewline($pointerBeforeElseIfPointer);
		$phpcsFile->fixer->addNewline($pointerBeforeElseIfPointer);

		$phpcsFile->fixer->replaceToken($elseIfPointer, sprintf('%sif', IndentationHelper::getIndentation($phpcsFile, $allConditionsPointers[0])));

		$phpcsFile->fixer->endChangeset();
	}

	private function processIf(File $phpcsFile, int $ifPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		if (!array_key_exists('scope_closer', $tokens[$ifPointer])) {
			// If without curly braces is not supported.
			return;
		}

		$nextPointer = TokenHelper::findNextExcluding($phpcsFile, T_WHITESPACE, $tokens[$ifPointer]['scope_closer'] + 1);
		if ($nextPointer === null || $tokens[$nextPointer]['code'] !== T_CLOSE_CURLY_BRACKET) {
			return;
		}

		$previousPointer = TokenHelper::findPreviousEffective($phpcsFile, $ifPointer - 1);
		if ($this->ignoreStandaloneIfInScope && in_array($tokens[$previousPointer]['code'], [T_OPEN_CURLY_BRACKET, T_COLON], true)) {
			return;
		}

		$scopePointer = $tokens[$nextPointer]['scope_condition'];

		if (!in_array($tokens[$scopePointer]['code'], [T_FUNCTION, T_CLOSURE, T_WHILE, T_DO, T_FOREACH, T_FOR], true)) {
			return;
		}

		if ($this->isEarlyExitInScope($phpcsFile, $tokens[$ifPointer]['scope_opener'], $tokens[$ifPointer]['scope_closer'])) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'Use early exit to reduce code nesting.',
			$ifPointer,
			self::CODE_EARLY_EXIT_NOT_USED
		);
		if (!$fix) {
			return;
		}

		$ifCode = $this->getScopeCode($phpcsFile, $ifPointer);
		$ifIndentation = IndentationHelper::getIndentation($phpcsFile, $ifPointer);
		$earlyExitCode = $this->getEarlyExitCode($tokens[$scopePointer]['code']);
		$earlyExitCodeIndentation = IndentationHelper::addIndentation($ifIndentation);

		$negativeIfCondition = ConditionHelper::getNegativeCondition($phpcsFile, $tokens[$ifPointer]['parenthesis_opener'], $tokens[$ifPointer]['parenthesis_closer']);

		$phpcsFile->fixer->beginChangeset();

		for ($i = $ifPointer; $i <= $tokens[$ifPointer]['scope_closer']; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}

		$afterIfCode = IndentationHelper::fixIndentation($ifCode, $phpcsFile->eolChar, $ifIndentation);

		$phpcsFile->fixer->addContent(
			$ifPointer,
			sprintf(
				'if %s {%s%s%s;%s%s}%s%s',
				$negativeIfCondition,
				$phpcsFile->eolChar,
				$earlyExitCodeIndentation,
				$earlyExitCode,
				$phpcsFile->eolChar,
				$ifIndentation,
				$phpcsFile->eolChar,
				$afterIfCode
			)
		);

		$phpcsFile->fixer->endChangeset();
	}

	private function getScopeCode(File $phpcsFile, int $scopePointer): string
	{
		$tokens = $phpcsFile->getTokens();
		return TokenHelper::getContent($phpcsFile, $tokens[$scopePointer]['scope_opener'] + 1, $tokens[$scopePointer]['scope_closer'] - 1);
	}

	/**
	 * @param string|int $code
	 * @return string
	 */
	private function getEarlyExitCode($code): string
	{
		if (in_array($code, [T_WHILE, T_DO, T_FOREACH, T_FOR], true)) {
			return 'continue';
		}

		return 'return';
	}

	private function findEarlyExitInScope(File $phpcsFile, int $startPointer, int $endPointer): ?int
	{
		$lastSemicolonInScopePointer = TokenHelper::findPreviousEffective($phpcsFile, $endPointer - 1);
		return $phpcsFile->getTokens()[$lastSemicolonInScopePointer]['code'] === T_SEMICOLON
			? TokenHelper::findPreviousLocal($phpcsFile, TokenHelper::$earlyExitTokenCodes, $lastSemicolonInScopePointer - 1, $startPointer)
			: null;
	}

	private function isEarlyExitInScope(File $phpcsFile, int $startPointer, int $endPointer): bool
	{
		return $this->findEarlyExitInScope($phpcsFile, $startPointer, $endPointer) !== null;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $conditionPointer
	 * @return int[]
	 */
	private function getAllConditionsPointers(File $phpcsFile, int $conditionPointer): array
	{
		$tokens = $phpcsFile->getTokens();

		$conditionsPointers = [$conditionPointer];

		if (isset($tokens[$conditionPointer]['scope_opener']) && $tokens[$tokens[$conditionPointer]['scope_opener']]['code'] === T_COLON) {
			// Alternative control structure syntax.
			throw new Exception(sprintf('"%s" without curly braces is not supported.', $tokens[$conditionPointer]['content']));
		}

		if ($tokens[$conditionPointer]['code'] !== T_IF) {
			$currentConditionPointer = $conditionPointer;
			do {
				$previousConditionCloseParenthesisPointer = TokenHelper::findPreviousEffective($phpcsFile, $currentConditionPointer - 1);
				$currentConditionPointer = $tokens[$previousConditionCloseParenthesisPointer]['scope_condition'];

				$conditionsPointers[] = $currentConditionPointer;
			} while ($tokens[$currentConditionPointer]['code'] !== T_IF);
		}

		if ($tokens[$conditionPointer]['code'] !== T_ELSE) {
			if (!array_key_exists('scope_closer', $tokens[$conditionPointer])) {
				throw new Exception(sprintf('"%s" without curly braces is not supported.', $tokens[$conditionPointer]['content']));
			}

			$currentConditionPointer = TokenHelper::findNextEffective($phpcsFile, $tokens[$conditionPointer]['scope_closer'] + 1);
			if ($currentConditionPointer !== null) {
				while (in_array($tokens[$currentConditionPointer]['code'], [T_ELSEIF, T_ELSE], true)) {
					$conditionsPointers[] = $currentConditionPointer;

					if (!array_key_exists('scope_closer', $tokens[$currentConditionPointer])) {
						throw new Exception(sprintf('"%s" without curly braces is not supported.', $tokens[$currentConditionPointer]['content']));
					}

					$currentConditionPointer = TokenHelper::findNextEffective($phpcsFile, $tokens[$currentConditionPointer]['scope_closer'] + 1);
				}
			}
		}

		sort($conditionsPointers);

		return $conditionsPointers;
	}

}
