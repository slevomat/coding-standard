<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Variables;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\TokenHelper;
use const T_AND_EQUAL;
use const T_CONCAT_EQUAL;
use const T_DIV_EQUAL;
use const T_EQUAL;
use const T_MINUS_EQUAL;
use const T_MOD_EQUAL;
use const T_MUL_EQUAL;
use const T_OR_EQUAL;
use const T_PLUS_EQUAL;
use const T_POW_EQUAL;
use const T_RETURN;
use const T_SEMICOLON;
use const T_SL_EQUAL;
use const T_SR_EQUAL;
use const T_VARIABLE;
use const T_XOR_EQUAL;
use function array_reverse;
use function count;
use function in_array;
use function sprintf;

class UselessVariableSniff implements Sniff
{

	public const CODE_USELESS_VARIABLE = 'UselessVariable';

	/**
	 * @return mixed[]
	 */
	public function register(): array
	{
		return [
			T_RETURN,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $returnPointer
	 */
	public function process(File $phpcsFile, $returnPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		/** @var int $variablePointer */
		$variablePointer = TokenHelper::findNextEffective($phpcsFile, $returnPointer + 1);
		if ($tokens[$variablePointer]['code'] !== T_VARIABLE) {
			return;
		}

		$pointerAfterVariable = TokenHelper::findNextEffective($phpcsFile, $variablePointer + 1);
		if ($tokens[$pointerAfterVariable]['code'] !== T_SEMICOLON) {
			return;
		}

		$variableName = $tokens[$variablePointer]['content'];

		$previousVariablePointer = null;
		for ($i = $returnPointer - 1; $i >= 0; $i--) {
			if (
				in_array($tokens[$i]['code'], TokenHelper::$functionTokenCodes, true)
				&& $this->isInSameScope($phpcsFile, $tokens[$i]['scope_opener'] + 1, $returnPointer)
			) {
				return;
			}

			if ($tokens[$i]['code'] !== T_VARIABLE) {
				continue;
			}

			if ($tokens[$i]['content'] !== $variableName) {
				continue;
			}

			if (!$this->isInSameScope($phpcsFile, $i, $variablePointer)) {
				continue;
			}

			$previousVariablePointer = $i;
			break;
		}

		if ($previousVariablePointer === null) {
			return;
		}

		$pointerAfterPreviousVariable = TokenHelper::findNextEffective($phpcsFile, $previousVariablePointer + 1);
		if (!in_array($tokens[$pointerAfterPreviousVariable]['code'], [
			T_EQUAL,
			T_PLUS_EQUAL,
			T_MINUS_EQUAL,
			T_MUL_EQUAL,
			T_DIV_EQUAL,
			T_POW_EQUAL,
			T_MOD_EQUAL,
			T_AND_EQUAL,
			T_OR_EQUAL,
			T_XOR_EQUAL,
			T_SL_EQUAL,
			T_SR_EQUAL,
			T_CONCAT_EQUAL,
		], true)) {
			return;
		}

		for ($i = $variablePointer + 1; $i < count($tokens); $i++) {
			if (
				in_array($tokens[$i]['code'], TokenHelper::$functionTokenCodes, true)
				&& $this->isInSameScope($phpcsFile, $variablePointer, $i)
			) {
				$i = $tokens[$i]['scope_closer'];
				continue;
			}

			if ($tokens[$i]['code'] !== T_VARIABLE) {
				continue;
			}

			if ($tokens[$i]['content'] !== $variableName) {
				continue;
			}

			if ($this->isInSameScope($phpcsFile, $variablePointer, $i)) {
				return;
			}
		}

		$phpcsFile->addError(sprintf('Useless variable %s.', $variableName), $previousVariablePointer, self::CODE_USELESS_VARIABLE);
	}

	private function isInSameScope(File $phpcsFile, int $firstPointer, int $secondPointer): bool
	{
		$tokens = $phpcsFile->getTokens();

		$getScopeLevel = function (int $pointer) use ($tokens): int {
			foreach (array_reverse($tokens[$pointer]['conditions'], true) as $conditionPointer => $conditionTokenCode) {
				if (!in_array($conditionTokenCode, TokenHelper::$functionTokenCodes, true)) {
					continue;
				}

				return $tokens[$conditionPointer]['level'];
			}

			return $tokens[$pointer]['level'];
		};

		return $getScopeLevel($firstPointer) === $getScopeLevel($secondPointer);
	}

}
