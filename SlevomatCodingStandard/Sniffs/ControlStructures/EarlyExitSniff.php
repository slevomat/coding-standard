<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Helpers\TokenHelper;

class EarlyExitSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	public const CODE_EARLY_EXIT_NOT_USED = 'EarlyExitNotUsed';
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

		if ($tokens[$pointer]['code'] === T_ELSE) {
			$this->processElse($phpcsFile, $pointer);
		} else {
			$this->processIf($phpcsFile, $pointer);
		}
	}

	private function processElse(\PHP_CodeSniffer\Files\File $phpcsFile, int $elsePointer): void
	{
		$tokens = $phpcsFile->getTokens();

		if (!$this->isEarlyExitInScope($phpcsFile, $tokens[$elsePointer]['scope_opener'], $tokens[$elsePointer]['scope_closer'])) {
			return;
		}

		$previousConditionCloseParenthesisPointer = TokenHelper::findPreviousEffective($phpcsFile, $elsePointer - 1);
		$previousConditionPointer = $tokens[$previousConditionCloseParenthesisPointer]['scope_condition'];

		if ($tokens[$previousConditionPointer]['code'] === T_ELSEIF) {
			return;
		}

		$ifPointer = $previousConditionPointer;

		if ($this->isEarlyExitInScope($phpcsFile, $tokens[$ifPointer]['scope_opener'], $tokens[$ifPointer]['scope_closer'])) {
			$fix = $phpcsFile->addFixableError(
				'Remove useless else to reduce code nesting.',
				$elsePointer,
				self::CODE_USELESS_ELSE
			);

			if (!$fix) {
				return;
			}

			$phpcsFile->fixer->beginChangeset();

			for ($i = $tokens[$ifPointer]['scope_closer'] + 1; $i <= $tokens[$elsePointer]['scope_closer']; $i++) {
				$phpcsFile->fixer->replaceToken($i, '');
			}

			$elseCode = $this->getScopeCode($phpcsFile, $elsePointer);
			$afterIfCode = $this->fixIndentation($elseCode, $phpcsFile->eolChar, $this->prepareIndentation($this->getIndentation($phpcsFile, $ifPointer)));

			$phpcsFile->fixer->addContent(
				$tokens[$elsePointer]['scope_closer'],
				sprintf(
					'%s%s',
					$phpcsFile->eolChar,
					$afterIfCode
				)
			);

			$phpcsFile->fixer->endChangeset();

		} else {
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
			$negativeIfCondition = $this->getNegativeCondition($phpcsFile, $tokens[$ifPointer]['parenthesis_opener'] + 1, $tokens[$ifPointer]['parenthesis_closer'] - 1);
			$afterIfCode = $this->fixIndentation($ifCode, $phpcsFile->eolChar, $this->getIndentation($phpcsFile, $ifPointer));

			$phpcsFile->fixer->addContent(
				$ifPointer,
				sprintf(
					'if (%s) {%s}%s%s',
					$negativeIfCondition,
					$elseCode,
					$phpcsFile->eolChar,
					$afterIfCode
				)
			);

			$phpcsFile->fixer->endChangeset();
		}
	}

	private function processIf(\PHP_CodeSniffer\Files\File $phpcsFile, int $ifPointer): void
	{
		$tokens = $phpcsFile->getTokens();

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

		$negativeIfCondition = $this->getNegativeCondition($phpcsFile, $tokens[$ifPointer]['parenthesis_opener'] + 1, $tokens[$ifPointer]['parenthesis_closer'] - 1);

		$phpcsFile->fixer->beginChangeset();

		for ($i = $ifPointer; $i <= $tokens[$ifPointer]['scope_closer']; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}

		$afterIfCode = $this->fixIndentation($ifCode, $phpcsFile->eolChar, $ifIndentation);

		$phpcsFile->fixer->addContent(
			$ifPointer,
			sprintf(
				'if (%s) {%s%s%s;%s%s}%s%s',
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

	private function getNegativeCondition(\PHP_CodeSniffer\Files\File $phpcsFile, int $startPointer, int $endPointer): string
	{
		$tokens = $phpcsFile->getTokens();

		$condition = TokenHelper::getContent($phpcsFile, $startPointer, $endPointer);

		$booleanNotPointer = TokenHelper::findNextEffective($phpcsFile, $startPointer);
		$pointerAfterBooleanNot = TokenHelper::findNextEffective($phpcsFile, $booleanNotPointer + 1);

		if ($tokens[$booleanNotPointer]['code'] === T_BOOLEAN_NOT && $tokens[$pointerAfterBooleanNot]['code'] === T_OPEN_PARENTHESIS) {
			return TokenHelper::getContent($phpcsFile, $pointerAfterBooleanNot + 1, $tokens[$pointerAfterBooleanNot]['parenthesis_closer'] - 1);
		}

		$booleanPointers = TokenHelper::findNextAll($phpcsFile, \PHP_CodeSniffer\Util\Tokens::$booleanOperators, $startPointer, $endPointer + 1);
		if (count($booleanPointers) > 0) {
			$uniqueBooleanOperators = array_unique(array_map(function (int $pointer) use ($tokens) {
				return $tokens[$pointer]['code'];
			}, $booleanPointers));

			if (count($uniqueBooleanOperators) > 1) {
				return sprintf('!(%s)', $condition);
			}

			if ($uniqueBooleanOperators[0] === T_LOGICAL_XOR) {
				return sprintf('!(%s)', $condition);
			}

			$negate = function (int $conditionBoundaryStart, int $conditionBoundaryEnd) use ($phpcsFile): string {
				/** @var int $conditionStartPointer */
				$conditionStartPointer = TokenHelper::findNextExcluding($phpcsFile, T_WHITESPACE, $conditionBoundaryStart);
				/** @var int $conditionEndPointer */
				$conditionEndPointer = TokenHelper::findPreviousExcluding($phpcsFile, T_WHITESPACE, $conditionBoundaryEnd);

				return sprintf(
					'%s%s%s',
					$conditionBoundaryStart !== $conditionStartPointer ? TokenHelper::getContent($phpcsFile, $conditionBoundaryStart, $conditionStartPointer - 1) : '',
					$this->getNegativeCondition($phpcsFile, $conditionStartPointer, $conditionEndPointer),
					$conditionBoundaryEnd !== $conditionEndPointer ? TokenHelper::getContent($phpcsFile, $conditionEndPointer + 1, $conditionBoundaryEnd) : ''
				);
			};

			$booleanOperatorReplacements = [
				T_BOOLEAN_AND => '||',
				T_BOOLEAN_OR => '&&',
				T_LOGICAL_AND => 'or',
				T_LOGICAL_OR => 'and',
			];

			$negativeCondition = $negate($startPointer, $booleanPointers[0] - 1);
			for ($i = 0; $i < count($booleanPointers); $i++) {
				$negativeCondition .= $booleanOperatorReplacements[$tokens[$booleanPointers[$i]]['code']];
				$negativeCondition .= $negate($booleanPointers[$i] + 1, $i + 1 < count($booleanPointers) ? $booleanPointers[$i + 1] - 1 : $endPointer);
			}

			return $negativeCondition;
		}

		if ($tokens[$booleanNotPointer]['code'] === T_BOOLEAN_NOT) {
			return preg_replace('~^!\\s*~', '', $condition);
		}

		if (TokenHelper::findNext($phpcsFile, [T_INSTANCEOF, T_BITWISE_AND], $startPointer, $endPointer + 1) !== null) {
			return sprintf('!(%s)', $condition);
		}

		$comparisonPointer = TokenHelper::findNext(
			$phpcsFile,
			[T_IS_EQUAL, T_IS_NOT_EQUAL, T_IS_IDENTICAL, T_IS_NOT_IDENTICAL, T_IS_SMALLER_OR_EQUAL, T_IS_GREATER_OR_EQUAL, T_LESS_THAN, T_GREATER_THAN],
			$startPointer,
			$endPointer + 1
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
			for ($i = $startPointer; $i <= $endPointer; $i++) {
				$negativeCondition .= array_key_exists($tokens[$i]['code'], $comparisonReplacements) ? $comparisonReplacements[$tokens[$i]['code']] : $tokens[$i]['content'];
			}

			return $negativeCondition;
		}

		return sprintf('!%s', $condition);
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

}
