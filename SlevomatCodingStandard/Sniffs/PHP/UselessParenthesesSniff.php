<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use SlevomatCodingStandard\Helpers\IdentificatorHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use const T_ANON_CLASS;
use const T_BOOLEAN_NOT;
use const T_CASE;
use const T_CLOSE_PARENTHESIS;
use const T_CLOSURE;
use const T_COLON;
use const T_DOLLAR;
use const T_EMPTY;
use const T_EVAL;
use const T_EXIT;
use const T_INLINE_THEN;
use const T_ISSET;
use const T_LIST;
use const T_NEW;
use const T_NS_SEPARATOR;
use const T_OPEN_PARENTHESIS;
use const T_PARENT;
use const T_SELF;
use const T_STATIC;
use const T_STRING;
use const T_UNSET;
use const T_USE;
use const T_VARIABLE;
use const T_WHITESPACE;
use function array_key_exists;
use function in_array;

class UselessParenthesesSniff implements Sniff
{

	public const CODE_USELESS_PARENTHESES = 'UselessParentheses';

	/** @var bool */
	public $ignoreComplexTernaryConditions = false;

	/**
	 * @return mixed[]
	 */
	public function register(): array
	{
		return [
			T_OPEN_PARENTHESIS,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $parenthesisOpenerPointer
	 */
	public function process(File $phpcsFile, $parenthesisOpenerPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		if (array_key_exists('parenthesis_owner', $tokens[$parenthesisOpenerPointer])) {
			return;
		}

		/** @var int $pointerBeforeParenthesisOpener */
		$pointerBeforeParenthesisOpener = TokenHelper::findPreviousEffective($phpcsFile, $parenthesisOpenerPointer - 1);
		if (in_array($tokens[$pointerBeforeParenthesisOpener]['code'], [T_VARIABLE, T_STRING, T_ISSET, T_UNSET, T_EMPTY, T_CLOSURE, T_USE, T_ANON_CLASS, T_SELF, T_STATIC, T_EXIT, T_CLOSE_PARENTHESIS, T_EVAL, T_LIST], true)) {
			return;
		}

		/** @var int $pointerAfterParenthesisOpener */
		$pointerAfterParenthesisOpener = TokenHelper::findNextEffective($phpcsFile, $parenthesisOpenerPointer + 1);
		if ($tokens[$pointerAfterParenthesisOpener]['code'] === T_NEW) {
			return;
		}

		if (IdentificatorHelper::findStartPointer($phpcsFile, $pointerBeforeParenthesisOpener) !== null) {
			return;
		}

		$this->checkParenthesesAroundConditionInTernaryOperator($phpcsFile, $parenthesisOpenerPointer);
		$this->checkParenthesesAroundCaseInSwitch($phpcsFile, $parenthesisOpenerPointer);
		$this->checkParenthesesAroundVariableOrFunctionCall($phpcsFile, $parenthesisOpenerPointer);
	}

	private function checkParenthesesAroundConditionInTernaryOperator(File $phpcsFile, int $parenthesisOpenerPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$parenthesisCloserPointer = $tokens[$parenthesisOpenerPointer]['parenthesis_closer'];

		$ternaryOperatorPointer = TokenHelper::findNextEffective($phpcsFile, $parenthesisCloserPointer + 1);
		if ($tokens[$ternaryOperatorPointer]['code'] !== T_INLINE_THEN) {
			return;
		}

		$pointerBeforeParenthesisOpener = TokenHelper::findPreviousEffective($phpcsFile, $parenthesisOpenerPointer - 1);
		if ($tokens[$pointerBeforeParenthesisOpener]['code'] === T_BOOLEAN_NOT) {
			return;
		}

		if ($this->ignoreComplexTernaryConditions) {
			if (TokenHelper::findNext($phpcsFile, Tokens::$booleanOperators, $parenthesisOpenerPointer + 1, $parenthesisCloserPointer) !== null) {
				return;
			}

			if (TokenHelper::findNextContent($phpcsFile, T_WHITESPACE, $phpcsFile->eolChar, $parenthesisOpenerPointer + 1, $parenthesisCloserPointer) !== null) {
				return;
			}
		}

		$contentStartPointer = TokenHelper::findNextEffective($phpcsFile, $parenthesisOpenerPointer + 1);
		$contentEndPointer = TokenHelper::findPreviousEffective($phpcsFile, $parenthesisCloserPointer - 1);

		for ($i = $contentStartPointer; $i <= $contentEndPointer; $i++) {
			if ($tokens[$i]['code'] === T_INLINE_THEN) {
				return;
			}
		}

		$fix = $phpcsFile->addFixableError('Useless parentheses.', $parenthesisOpenerPointer, self::CODE_USELESS_PARENTHESES);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();
		for ($i = $parenthesisOpenerPointer; $i < $contentStartPointer; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}
		for ($i = $contentEndPointer + 1; $i <= $parenthesisCloserPointer; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}
		$phpcsFile->fixer->endChangeset();
	}

	private function checkParenthesesAroundCaseInSwitch(File $phpcsFile, int $parenthesisOpenerPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$pointerBeforeParenthesisOpener = TokenHelper::findPreviousEffective($phpcsFile, $parenthesisOpenerPointer - 1);
		if ($tokens[$pointerBeforeParenthesisOpener]['code'] !== T_CASE) {
			return;
		}

		$pointerAfterParenthesisCloser = TokenHelper::findNextEffective($phpcsFile, $tokens[$parenthesisOpenerPointer]['parenthesis_closer'] + 1);
		if ($tokens[$pointerAfterParenthesisCloser]['code'] !== T_COLON) {
			return;
		}

		$fix = $phpcsFile->addFixableError('Useless parentheses.', $parenthesisOpenerPointer, self::CODE_USELESS_PARENTHESES);

		if (!$fix) {
			return;
		}

		$contentStartPointer = TokenHelper::findNextEffective($phpcsFile, $parenthesisOpenerPointer + 1);
		$contentEndPointer = TokenHelper::findPreviousEffective($phpcsFile, $tokens[$parenthesisOpenerPointer]['parenthesis_closer'] - 1);

		$phpcsFile->fixer->beginChangeset();
		for ($i = $parenthesisOpenerPointer; $i < $contentStartPointer; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}
		for ($i = $contentEndPointer + 1; $i <= $tokens[$parenthesisOpenerPointer]['parenthesis_closer']; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}
		$phpcsFile->fixer->endChangeset();
	}

	private function checkParenthesesAroundVariableOrFunctionCall(File $phpcsFile, int $parenthesisOpenerPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$casePointer = TokenHelper::findPreviousEffective($phpcsFile, $parenthesisOpenerPointer - 1);
		if ($tokens[$casePointer]['code'] === T_CASE) {
			return;
		}

		$pointerAfterParenthesisCloser = TokenHelper::findNextEffective($phpcsFile, $tokens[$parenthesisOpenerPointer]['parenthesis_closer'] + 1);
		if (in_array($tokens[$pointerAfterParenthesisCloser]['code'], [T_INLINE_THEN, T_OPEN_PARENTHESIS], true)) {
			return;
		}

		/** @var int $contentStartPointer */
		$contentStartPointer = TokenHelper::findNextEffective($phpcsFile, $parenthesisOpenerPointer + 1);
		$notBooleanNotOperatorPointer = $contentStartPointer;

		if ($tokens[$contentStartPointer]['code'] === T_BOOLEAN_NOT) {
			/** @var int $notBooleanNotOperatorPointer */
			$notBooleanNotOperatorPointer = TokenHelper::findNextEffective($phpcsFile, $contentStartPointer + 1);
		}

		if (in_array($tokens[$notBooleanNotOperatorPointer]['code'], [T_NS_SEPARATOR, T_STRING, T_SELF, T_STATIC, T_PARENT, T_VARIABLE, T_DOLLAR], true)) {
			$contentEndPointer = IdentificatorHelper::findEndPointer($phpcsFile, $notBooleanNotOperatorPointer);

			if ($contentEndPointer === null && $tokens[$notBooleanNotOperatorPointer]['code'] === T_STRING) {
				$nextPointer = TokenHelper::findNextEffective($phpcsFile, $contentStartPointer + 1);
				if ($tokens[$nextPointer]['code'] === T_OPEN_PARENTHESIS) {
					$contentEndPointer = $contentStartPointer;
				}
			}

			do {
				$nextPointer = TokenHelper::findNextEffective($phpcsFile, $contentEndPointer + 1);

				if ($tokens[$nextPointer]['code'] !== T_OPEN_PARENTHESIS) {
					break;
				}

				$contentEndPointer = $tokens[$nextPointer]['parenthesis_closer'];
			} while (true);
		} else {
			$nextPointer = TokenHelper::findNext($phpcsFile, T_OPEN_PARENTHESIS, $notBooleanNotOperatorPointer + 1);
			$contentEndPointer = $tokens[$nextPointer]['parenthesis_closer'];
		}

		$pointerAfterContent = TokenHelper::findNextEffective($phpcsFile, $contentEndPointer + 1);

		if ($pointerAfterContent !== $tokens[$parenthesisOpenerPointer]['parenthesis_closer']) {
			return;
		}

		$fix = $phpcsFile->addFixableError('Useless parentheses.', $parenthesisOpenerPointer, self::CODE_USELESS_PARENTHESES);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();
		for ($i = $parenthesisOpenerPointer; $i < $contentStartPointer; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}
		for ($i = $contentEndPointer + 1; $i <= $tokens[$parenthesisOpenerPointer]['parenthesis_closer']; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}
		$phpcsFile->fixer->endChangeset();
	}

}
