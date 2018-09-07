<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Arrays;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\ScopeHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use const T_ARRAY;
use const T_CLOSURE;
use const T_DOUBLE_COLON;
use const T_EQUAL;
use const T_OBJECT_OPERATOR;
use const T_OPEN_SHORT_ARRAY;
use const T_OPEN_SQUARE_BRACKET;
use const T_OPEN_TAG;
use const T_USE;
use const T_VARIABLE;
use function array_reverse;
use function count;
use function in_array;

class DisallowImplicitArrayCreationSniff implements Sniff
{

	public const CODE_IMPLICIT_ARRAY_CREATION_USED = 'ImplicitArrayCreationUsed';

	/**
	 * @return mixed[]
	 */
	public function register(): array
	{
		return [
			T_OPEN_SQUARE_BRACKET,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $bracketOpenerPointer
	 */
	public function process(File $phpcsFile, $bracketOpenerPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$assigmentPointer = TokenHelper::findNextEffective($phpcsFile, $tokens[$bracketOpenerPointer]['bracket_closer'] + 1);
		if ($tokens[$assigmentPointer]['code'] !== T_EQUAL) {
			return;
		}

		/** @var int $variablePointer */
		$variablePointer = TokenHelper::findPreviousEffective($phpcsFile, $bracketOpenerPointer - 1);
		if ($tokens[$variablePointer]['code'] !== T_VARIABLE) {
			return;
		}

		$pointerBeforeVariable = TokenHelper::findPreviousEffective($phpcsFile, $variablePointer - 1);
		if (in_array($tokens[$pointerBeforeVariable]['code'], [T_OBJECT_OPERATOR, T_DOUBLE_COLON], true)) {
			return;
		}

		$scopeOwnerPointer = null;
		foreach (array_reverse($tokens[$variablePointer]['conditions'], true) as $conditionPointer => $conditionTokenCode) {
			if (!in_array($conditionTokenCode, TokenHelper::$functionTokenCodes, true)) {
				continue;
			}

			$scopeOwnerPointer = $conditionPointer;
			break;
		}

		if ($scopeOwnerPointer === null) {
			$scopeOwnerPointer = TokenHelper::findPrevious($phpcsFile, T_OPEN_TAG, $variablePointer - 1);
		}

		$scopeOpenerPointer = $tokens[$scopeOwnerPointer]['code'] === T_OPEN_TAG ? $scopeOwnerPointer : $tokens[$scopeOwnerPointer]['scope_opener'];
		$scopeCloserPointer = $tokens[$scopeOwnerPointer]['code'] === T_OPEN_TAG ? count($tokens) - 1 : $tokens[$scopeOwnerPointer]['scope_closer'];

		if (in_array($tokens[$scopeOwnerPointer]['code'], TokenHelper::$functionTokenCodes, true)) {
			if ($this->isParameter($phpcsFile, $scopeOwnerPointer, $variablePointer)) {
				return;
			}

			if ($tokens[$scopeOwnerPointer]['code'] === T_CLOSURE && $this->isInheritedVariable($phpcsFile, $scopeOwnerPointer, $variablePointer)) {
				return;
			}
		}

		if ($this->hasExplicitCreation($phpcsFile, $scopeOpenerPointer, $scopeCloserPointer, $variablePointer)) {
			return;
		}

		$phpcsFile->addError('Implicit array creation is disallowed.', $variablePointer, self::CODE_IMPLICIT_ARRAY_CREATION_USED);
	}

	private function isParameter(File $phpcsFile, int $functionPointer, int $variablePointer): bool
	{
		$tokens = $phpcsFile->getTokens();
		$variableName = $tokens[$variablePointer]['content'];

		$parameterPointer = TokenHelper::findNextContent($phpcsFile, T_VARIABLE, $variableName, $tokens[$functionPointer]['parenthesis_opener'] + 1, $tokens[$functionPointer]['parenthesis_closer']);
		return $parameterPointer !== null;
	}

	private function isInheritedVariable(File $phpcsFile, int $closurePointer, int $variablePointer): bool
	{
		$tokens = $phpcsFile->getTokens();
		$variableName = $tokens[$variablePointer]['content'];

		$usePointer = TokenHelper::findNext($phpcsFile, T_USE, $tokens[$closurePointer]['parenthesis_closer'] + 1, $tokens[$closurePointer]['scope_opener']);
		if ($usePointer === null) {
			return false;
		}

		$parenthesisOpenerPointer = TokenHelper::findNextEffective($phpcsFile, $usePointer + 1);

		$inheritedVariablePointer = TokenHelper::findNextContent($phpcsFile, T_VARIABLE, $variableName, $parenthesisOpenerPointer + 1, $tokens[$parenthesisOpenerPointer]['parenthesis_closer']);
		return $inheritedVariablePointer !== null;
	}

	private function hasExplicitCreation(File $phpcsFile, int $scopeOpenerPointer, int $scopeCloserPointer, int $variablePointer): bool
	{
		$tokens = $phpcsFile->getTokens();

		$variableName = $tokens[$variablePointer]['content'];

		for ($i = $scopeOpenerPointer + 1; $i < $scopeCloserPointer; $i++) {
			if ($tokens[$i]['code'] !== T_VARIABLE) {
				continue;
			}

			if ($tokens[$i]['content'] !== $variableName) {
				continue;
			}

			$assigmentPointer = TokenHelper::findNextEffective($phpcsFile, $i + 1);
			if ($tokens[$assigmentPointer]['code'] !== T_EQUAL) {
				continue;
			}

			$arrayPointer = TokenHelper::findNextEffective($phpcsFile, $assigmentPointer + 1);
			if (!in_array($tokens[$arrayPointer]['code'], [T_ARRAY, T_OPEN_SHORT_ARRAY], true)) {
				continue;
			}

			if (ScopeHelper::isInSameScope($phpcsFile, $variablePointer, $i)) {
				return true;
			}
		}

		return false;
	}

}
