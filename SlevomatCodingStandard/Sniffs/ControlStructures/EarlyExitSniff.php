<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Helpers\TokenHelper;

class EarlyExitSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	public const CODE_EARLY_EXIT_NOT_USED = 'EarlyExitNotUsed';
	public const CODE_USELESS_ELSE = 'UselessElse';

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

		$earlyExitTokenCodes = [T_RETURN, T_CONTINUE, T_BREAK, T_THROW, T_YIELD, T_EXIT];

		$lastSemicolonInElseScopePointer = TokenHelper::findPreviousEffective($phpcsFile, $tokens[$elsePointer]['scope_closer'] - 1);
		if ($tokens[$lastSemicolonInElseScopePointer]['code'] !== T_SEMICOLON) {
			return;
		}

		$earlyExitInElseScopePointer = TokenHelper::findPreviousLocal($phpcsFile, $earlyExitTokenCodes, $lastSemicolonInElseScopePointer - 1, $tokens[$elsePointer]['scope_opener']);
		if ($earlyExitInElseScopePointer === null) {
			return;
		}

		$previousConditionCloseParenthesisPointer = TokenHelper::findPreviousEffective($phpcsFile, $elsePointer - 1);
		$previousConditionPointer = $tokens[$previousConditionCloseParenthesisPointer]['scope_condition'];

		if ($tokens[$previousConditionPointer]['code'] === T_ELSEIF) {
			return;
		}

		$ifPointer = $previousConditionPointer;

		$lastSemicolonInIfScopePointer = TokenHelper::findPreviousEffective($phpcsFile, $tokens[$ifPointer]['scope_closer'] - 1);
		$isEarlyExitInIfScope = $tokens[$lastSemicolonInIfScopePointer]['code'] === T_SEMICOLON
			? TokenHelper::findPreviousLocal($phpcsFile, $earlyExitTokenCodes, $lastSemicolonInIfScopePointer - 1, $tokens[$ifPointer]['scope_opener']) !== null
			: false;

		if ($isEarlyExitInIfScope) {
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
			$negativeIfCondition = $this->getNegativeIfCondition($phpcsFile, $ifPointer);
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
		if ($tokens[$nextPointer]['code'] !== T_CLOSE_CURLY_BRACKET) {
			return;
		}

		$scopePointer = $tokens[$nextPointer]['scope_condition'];

		if (!in_array($tokens[$scopePointer]['code'], [T_FUNCTION, T_CLOSURE, T_WHILE, T_DO, T_FOREACH, T_FOR], true)) {
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

		$negativeIfCondition = $this->getNegativeIfCondition($phpcsFile, $ifPointer);

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
		return $identation . ($identation[0] === "\t" ? "\t" : '    ');
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

	private function getNegativeIfCondition(\PHP_CodeSniffer\Files\File $phpcsFile, int $ifPointer): string
	{
		$tokens = $phpcsFile->getTokens();

		$ifCondition = TokenHelper::getContent($phpcsFile, $tokens[$ifPointer]['parenthesis_opener'] + 1, $tokens[$ifPointer]['parenthesis_closer'] - 1);

		$booleanPointer = TokenHelper::findNext($phpcsFile, \PHP_CodeSniffer\Util\Tokens::$booleanOperators, $tokens[$ifPointer]['parenthesis_opener'] + 1, $tokens[$ifPointer]['parenthesis_closer']);
		if ($booleanPointer !== null) {
			return sprintf('!(%s)', $ifCondition);
		}

		$booleanNotPointer = TokenHelper::findNextEffective($phpcsFile, $tokens[$ifPointer]['parenthesis_opener'] + 1);
		if ($tokens[$booleanNotPointer]['code'] === T_BOOLEAN_NOT) {
			return ltrim($ifCondition, '!');
		}

		if (TokenHelper::findNext($phpcsFile, T_INSTANCEOF, $tokens[$ifPointer]['parenthesis_opener'] + 1, $tokens[$ifPointer]['parenthesis_closer']) !== null) {
			return sprintf('!(%s)', $ifCondition);
		}

		$comparisonPointer = TokenHelper::findNext($phpcsFile, [T_IS_EQUAL, T_IS_NOT_EQUAL, T_IS_IDENTICAL, T_IS_NOT_IDENTICAL, T_IS_SMALLER_OR_EQUAL, T_IS_GREATER_OR_EQUAL, T_LESS_THAN, T_GREATER_THAN], $tokens[$ifPointer]['parenthesis_opener'] + 1, $tokens[$ifPointer]['parenthesis_closer']);
		if ($comparisonPointer !== null) {
			$negativeIfCondition = '';

			for ($i = $tokens[$ifPointer]['parenthesis_opener'] + 1; $i < $tokens[$ifPointer]['parenthesis_closer']; $i++) {
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

				$negativeIfCondition .= array_key_exists($tokens[$i]['code'], $comparisonReplacements) ? $comparisonReplacements[$tokens[$i]['code']] : $tokens[$i]['content'];
			}

			return $negativeIfCondition;
		}

		return sprintf('!%s', $ifCondition);
	}

	private function fixIndentation(string $code, string $eolChar, string $defaultIndentation): string
	{
		return implode($eolChar, array_map(function (string $line) use ($defaultIndentation): string {
			if ($line === '') {
				return $line;
			}

			if ($line[0] === "\t") {
				return substr($line, 1);
			}

			if (substr($line, 0, 4) === '    ') {
				return substr($line, 4);
			}

			return $defaultIndentation . ltrim($line);
		}, explode($eolChar, rtrim($code))));
	}

}
