<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Helpers\TokenHelper;

class EarlyExitSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	public const CODE_EARLY_EXIT_NOT_USED = 'EarlyExitNotUsed';
	public const CODE_USELESS_ELSEIF = 'UselessElseIf';
	public const CODE_USELESS_ELSE = 'UselessElse';

	private const TAB_INDENT = "\t";
	private const SPACES_INDENT = '    ';

	/**
	 * @return mixed[]
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
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $pointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $pointer): void
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

	private function processElse(\PHP_CodeSniffer\Files\File $phpcsFile, int $elsePointer): void
	{
		$tokens = $phpcsFile->getTokens();

		if (!array_key_exists('scope_opener', $tokens[$elsePointer])) {
			throw new \Exception('"else" without curly braces is not supported.');
		}

		$allConditionsPointers = $this->getAllConditionsPointers($phpcsFile, $elsePointer);

		foreach ($allConditionsPointers as $conditionPointer) {
			if ($tokens[$conditionPointer]['code'] === T_IF) {
				continue;
			}

			if (!$this->isEarlyExitInScope($phpcsFile, $tokens[$conditionPointer]['scope_opener'], $tokens[$conditionPointer]['scope_closer'])) {
				return;
			}
		}

		$previousConditionPointer = $allConditionsPointers[count($allConditionsPointers) - 2];

		if ($this->isEarlyExitInScope($phpcsFile, $tokens[$previousConditionPointer]['scope_opener'], $tokens[$previousConditionPointer]['scope_closer'])) {
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
			$afterIfCode = $this->fixIndentation($elseCode, $phpcsFile->eolChar, $this->prepareIndentation($this->getIndentation($phpcsFile, $previousConditionPointer)));

			$phpcsFile->fixer->addContent(
				$tokens[$elsePointer]['scope_closer'],
				sprintf(
					'%s%s',
					$phpcsFile->eolChar,
					$afterIfCode
				)
			);

			$phpcsFile->fixer->endChangeset();

		} elseif ($tokens[$previousConditionPointer]['code'] === T_IF) {

			$ifPointer = $previousConditionPointer;

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
			$negativeIfCondition = $this->getNegativeCondition($phpcsFile, $tokens[$ifPointer]['parenthesis_opener'], $tokens[$ifPointer]['parenthesis_closer']);
			$afterIfCode = $this->fixIndentation($ifCode, $phpcsFile->eolChar, $this->getIndentation($phpcsFile, $ifPointer));

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
		}
	}

	private function processElseIf(\PHP_CodeSniffer\Files\File $phpcsFile, int $elseIfPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$allConditionsPointers = $this->getAllConditionsPointers($phpcsFile, $elseIfPointer);

		foreach ($allConditionsPointers as $conditionPointer) {
			if (!$this->isEarlyExitInScope($phpcsFile, $tokens[$conditionPointer]['scope_opener'], $tokens[$conditionPointer]['scope_closer'])) {
				return;
			}
		}

		$lastConditionPointer = $allConditionsPointers[count($allConditionsPointers) - 1];

		$pointerAfterLastCondition = TokenHelper::findNextEffective($phpcsFile, $tokens[$lastConditionPointer]['scope_closer'] + 1);
		if ($pointerAfterLastCondition === null || $tokens[$pointerAfterLastCondition]['code'] !== T_CLOSE_CURLY_BRACKET) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'Remove useless elseif to reduce code nesting.',
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

		$phpcsFile->fixer->replaceToken($elseIfPointer, sprintf('%sif', $this->getIndentation($phpcsFile, $allConditionsPointers[0])));

		$phpcsFile->fixer->endChangeset();
	}

	private function processIf(\PHP_CodeSniffer\Files\File $phpcsFile, int $ifPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		if (!array_key_exists('scope_closer', $tokens[$ifPointer])) {
			throw new \Exception('"if" without curly braces is not supported.');
		}

		$nextPointer = TokenHelper::findNextExcluding($phpcsFile, T_WHITESPACE, $tokens[$ifPointer]['scope_closer'] + 1);
		if ($nextPointer === null || $tokens[$nextPointer]['code'] !== T_CLOSE_CURLY_BRACKET) {
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
		$ifIndentation = $this->getIndentation($phpcsFile, $ifPointer);
		$earlyExitCode = $this->getEarlyExitCode($tokens[$scopePointer]['code']);
		$earlyExitCodeIndentation = $this->prepareIndentation($ifIndentation);

		$negativeIfCondition = $this->getNegativeCondition($phpcsFile, $tokens[$ifPointer]['parenthesis_opener'], $tokens[$ifPointer]['parenthesis_closer']);

		$phpcsFile->fixer->beginChangeset();

		for ($i = $ifPointer; $i <= $tokens[$ifPointer]['scope_closer']; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}

		$afterIfCode = $this->fixIndentation($ifCode, $phpcsFile->eolChar, $ifIndentation);

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

	private function getIndentation(\PHP_CodeSniffer\Files\File $phpcsFile, int $pointer): string
	{
		$tokens = $phpcsFile->getTokens();

		$indentation = '';
		$actualPointer = $pointer - 1;
		while ($tokens[$actualPointer]['code'] === T_WHITESPACE) {
			$indentation .= $tokens[$actualPointer]['content'];
			$actualPointer--;
		}

		return trim($indentation, "\n\r");
	}

	private function prepareIndentation(string $identation): string
	{
		return $identation . ($identation[0] === self::TAB_INDENT ? self::TAB_INDENT : self::SPACES_INDENT);
	}

	private function getScopeCode(\PHP_CodeSniffer\Files\File $phpcsFile, int $scopePointer): string
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

	private function getNegativeConditionPart(\PHP_CodeSniffer\Files\File $phpcsFile, int $conditionBoundaryStartPointer, int $conditionBoundaryEndPointer, bool $nested): string
	{
		$tokens = $phpcsFile->getTokens();

		$condition = TokenHelper::getContent($phpcsFile, $conditionBoundaryStartPointer, $conditionBoundaryEndPointer);

		$pointerAfterConditionStart = TokenHelper::findNextEffective($phpcsFile, $conditionBoundaryStartPointer);
		$booleanPointers = TokenHelper::findNextAll($phpcsFile, \PHP_CodeSniffer\Util\Tokens::$booleanOperators, $conditionBoundaryStartPointer, $conditionBoundaryEndPointer + 1);

		if ($tokens[$pointerAfterConditionStart]['code'] === T_BOOLEAN_NOT) {
			if ($nested && count($booleanPointers) > 0) {
				return $this->removeBooleanNot($condition);
			}

			$pointerAfterBooleanNot = TokenHelper::findNextEffective($phpcsFile, $pointerAfterConditionStart + 1);
			if ($tokens[$pointerAfterBooleanNot]['code'] === T_OPEN_PARENTHESIS) {
				$pointerAfterParenthesisCloser = TokenHelper::findNextEffective($phpcsFile, $tokens[$pointerAfterBooleanNot]['parenthesis_closer'] + 1, $conditionBoundaryEndPointer + 1);
				if ($pointerAfterParenthesisCloser === null || $pointerAfterParenthesisCloser === $conditionBoundaryEndPointer) {
					return TokenHelper::getContent($phpcsFile, $pointerAfterBooleanNot + 1, $tokens[$pointerAfterBooleanNot]['parenthesis_closer'] - 1);
				}
			}
		}

		if (count($booleanPointers) > 0) {
			return $this->getNegativeLogicalCondition($phpcsFile, $conditionBoundaryStartPointer, $conditionBoundaryEndPointer);
		}

		if ($tokens[$pointerAfterConditionStart]['code'] === T_BOOLEAN_NOT) {
			return $this->removeBooleanNot($condition);
		}

		if (TokenHelper::findNext($phpcsFile, [T_INSTANCEOF, T_BITWISE_AND], $conditionBoundaryStartPointer, $conditionBoundaryEndPointer + 1) !== null) {
			return sprintf('!(%s)', $condition);
		}

		$comparisonPointer = TokenHelper::findNext(
			$phpcsFile,
			[T_IS_EQUAL, T_IS_NOT_EQUAL, T_IS_IDENTICAL, T_IS_NOT_IDENTICAL, T_IS_SMALLER_OR_EQUAL, T_IS_GREATER_OR_EQUAL, T_LESS_THAN, T_GREATER_THAN],
			$conditionBoundaryStartPointer,
			$conditionBoundaryEndPointer + 1
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
				$negativeCondition .= array_key_exists($tokens[$i]['code'], $comparisonReplacements) ? $comparisonReplacements[$tokens[$i]['code']] : $tokens[$i]['content'];
			}

			return $negativeCondition;
		}

		return sprintf('!%s', $condition);
	}

	private function removeBooleanNot(string $condition): string
	{
		return preg_replace('~^!\\s*~', '', $condition);
	}

	private function getNegativeLogicalCondition(\PHP_CodeSniffer\Files\File $phpcsFile, int $conditionBoundaryStartPointer, int $conditionBoundaryEndPointer): string
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

		do {
			$actualPointer = TokenHelper::findNext($phpcsFile, array_merge([T_OPEN_PARENTHESIS, T_CLOSE_PARENTHESIS], \PHP_CodeSniffer\Util\Tokens::$booleanOperators), $actualPointer, $conditionBoundaryEndPointer + 1);

			if ($actualPointer === null) {
				break;
			}

			if ($tokens[$actualPointer]['code'] === T_OPEN_PARENTHESIS) {
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

			$negativeCondition .= $this->getNegativeCondition($phpcsFile, $nestedConditionStartPointer, $actualPointer - 1, true);
			$negativeCondition .= $booleanOperatorReplacements[$tokens[$actualPointer]['code']];

			$nestedConditionStartPointer = $actualPointer + 1;
			$actualPointer++;

		} while (true);

		$negativeCondition .= $this->getNegativeCondition($phpcsFile, $nestedConditionStartPointer, $conditionBoundaryEndPointer, true);

		return $negativeCondition;
	}

	private function getNegativeCondition(\PHP_CodeSniffer\Files\File $phpcsFile, int $conditionBoundaryStartPointer, int $conditionBoundaryEndPointer, bool $nested = false): string
	{
		/** @var int $conditionStartPointer */
		$conditionStartPointer = TokenHelper::findNextEffective($phpcsFile, $conditionBoundaryStartPointer);
		/** @var int $conditionEndPointer */
		$conditionEndPointer = TokenHelper::findPreviousEffective($phpcsFile, $conditionBoundaryEndPointer);

		$tokens = $phpcsFile->getTokens();
		if ($tokens[$conditionStartPointer]['code'] === T_OPEN_PARENTHESIS && $tokens[$conditionStartPointer]['parenthesis_closer'] === $conditionEndPointer) {
			/** @var int $conditionStartPointer */
			$conditionStartPointer = TokenHelper::findNextEffective($phpcsFile, $conditionStartPointer + 1);
			/** @var int $conditionEndPointer */
			$conditionEndPointer = TokenHelper::findPreviousEffective($phpcsFile, $conditionEndPointer - 1);
		}

		return sprintf(
			'%s%s%s',
			$conditionBoundaryStartPointer !== $conditionStartPointer ? TokenHelper::getContent($phpcsFile, $conditionBoundaryStartPointer, $conditionStartPointer - 1) : '',
			$this->getNegativeConditionPart($phpcsFile, $conditionStartPointer, $conditionEndPointer, $nested),
			$conditionBoundaryEndPointer !== $conditionEndPointer ? TokenHelper::getContent($phpcsFile, $conditionEndPointer + 1, $conditionBoundaryEndPointer) : ''
		);
	}

	private function fixIndentation(string $code, string $eolChar, string $defaultIndentation): string
	{
		return implode($eolChar, array_map(function (string $line) use ($defaultIndentation): string {
			if ($line === '') {
				return $line;
			}

			if ($line[0] === self::TAB_INDENT) {
				return substr($line, 1);
			}

			if (substr($line, 0, 4) === self::SPACES_INDENT) {
				return substr($line, 4);
			}

			return $defaultIndentation . ltrim($line);
		}, explode($eolChar, rtrim($code))));
	}

	private function isEarlyExitInScope(\PHP_CodeSniffer\Files\File $phpcsFile, int $startPointer, int $endPointer): bool
	{
		$lastSemicolonInScopePointer = TokenHelper::findPreviousEffective($phpcsFile, $endPointer - 1);
		return $phpcsFile->getTokens()[$lastSemicolonInScopePointer]['code'] === T_SEMICOLON
			? TokenHelper::findPreviousLocal($phpcsFile, TokenHelper::$earlyExitTokenCodes, $lastSemicolonInScopePointer - 1, $startPointer) !== null
			: false;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $conditionPointer
	 * @return int[]
	 */
	private function getAllConditionsPointers(\PHP_CodeSniffer\Files\File $phpcsFile, int $conditionPointer): array
	{
		$tokens = $phpcsFile->getTokens();

		$conditionsPointers = [$conditionPointer];

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
				throw new \Exception(sprintf('"%s" without curly braces is not supported.', $tokens[$conditionPointer]['content']));
			}

			$currentConditionPointer = TokenHelper::findNextEffective($phpcsFile, $tokens[$conditionPointer]['scope_closer'] + 1);
			if ($currentConditionPointer !== null) {
				while (in_array($tokens[$currentConditionPointer]['code'], [T_ELSEIF, T_ELSE], true)) {
					$conditionsPointers[] = $currentConditionPointer;
					$currentConditionPointer = TokenHelper::findNextEffective($phpcsFile, $tokens[$currentConditionPointer]['scope_closer'] + 1);
				}
			}
		}

		sort($conditionsPointers);

		return $conditionsPointers;
	}

}
