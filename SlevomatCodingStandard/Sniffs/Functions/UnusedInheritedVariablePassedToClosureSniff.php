<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\TokenHelper;
use const T_CLOSE_PARENTHESIS;
use const T_CLOSURE;
use const T_COMMA;
use const T_OPEN_PARENTHESIS;
use const T_USE;
use const T_VARIABLE;
use function array_keys;
use function array_reverse;
use function sprintf;

class UnusedInheritedVariablePassedToClosureSniff implements Sniff
{

	public const CODE_UNUSED_INHERITED_VARIABLE = 'UnusedInheritedVariable';

	/**
	 * @return mixed[]
	 */
	public function register(): array
	{
		return [
			T_USE,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $usePointer
	 */
	public function process(File $phpcsFile, $usePointer): void
	{
		$tokens = $phpcsFile->getTokens();

		/** @var int $parenthesisOpenerPointer */
		$parenthesisOpenerPointer = TokenHelper::findNextEffective($phpcsFile, $usePointer + 1);
		if ($tokens[$parenthesisOpenerPointer]['code'] !== T_OPEN_PARENTHESIS) {
			return;
		}

		$closurePointer = TokenHelper::findPrevious($phpcsFile, T_CLOSURE, $usePointer - 1);

		$currentPointer = $parenthesisOpenerPointer + 1;
		do {
			$variablePointer = TokenHelper::findNext($phpcsFile, T_VARIABLE, $currentPointer, $tokens[$parenthesisOpenerPointer]['parenthesis_closer']);
			if ($variablePointer === null) {
				break;
			}

			$this->checkVariableUsage(
				$phpcsFile,
				$usePointer,
				$parenthesisOpenerPointer,
				$tokens[$parenthesisOpenerPointer]['parenthesis_closer'],
				$variablePointer,
				$tokens[$closurePointer]['scope_opener'],
				$tokens[$closurePointer]['scope_closer']
			);

			$currentPointer = $variablePointer + 1;
		} while (true);
	}

	private function checkVariableUsage(
		File $phpcsFile,
		int $usePointer,
		int $useParenthesisOpenerPointer,
		int $useParenthesisCloserPointer,
		int $variablePointer,
		int $scopeOpener,
		int $scopeCloser
	): void
	{
		$tokens = $phpcsFile->getTokens();

		$variableName = $tokens[$variablePointer]['content'];

		$isInSameLevel = function (int $pointer) use ($variablePointer, $tokens): bool {
			foreach (array_reverse($tokens[$pointer]['conditions'], true) as $conditionPointer => $conditionTokenCode) {
				if ($tokens[$conditionPointer]['level'] <= $tokens[$variablePointer]['level']) {
					break;
				}

				if ($conditionTokenCode === T_CLOSURE) {
					return false;
				}
			}

			return true;
		};

		$currentPointer = $scopeOpener + 1;
		do {
			$usedVariablePointer = TokenHelper::findNextContent($phpcsFile, T_VARIABLE, $variableName, $currentPointer, $scopeCloser - 1);
			if ($usedVariablePointer === null) {
				break;
			}

			if (isset($tokens[$usedVariablePointer]['nested_parenthesis'])) {
				$parenthesisOpenerPointer = array_reverse(array_keys($tokens[$usedVariablePointer]['nested_parenthesis']))[0];
				if (isset($tokens[$parenthesisOpenerPointer]['parenthesis_owner'])) {
					$parenthesisOwnerPointer = $tokens[$parenthesisOpenerPointer]['parenthesis_owner'];
					if ($tokens[$parenthesisOwnerPointer]['code'] === T_CLOSURE) {
						// Parameter
						$currentPointer = $usedVariablePointer + 1;
						continue;
					}
				}
			}

			if ($isInSameLevel($usedVariablePointer)) {
				return;
			}

			$currentPointer = $usedVariablePointer + 1;
		} while (true);

		$fix = $phpcsFile->addFixableError(
			sprintf('Unused inherited variable %s passed to closure.', $variableName),
			$variablePointer,
			self::CODE_UNUSED_INHERITED_VARIABLE
		);

		if (!$fix) {
			return;
		}

		$fixStartPointer = $variablePointer;
		do {
			if ($tokens[$fixStartPointer - 1]['code'] === T_OPEN_PARENTHESIS) {
				break;
			}

			$fixStartPointer--;

			if ($tokens[$fixStartPointer]['code'] === T_COMMA) {
				break;
			}
		} while (true);

		$fixEndPointer = $variablePointer;
		do {
			if ($tokens[$fixEndPointer + 1]['code'] === T_CLOSE_PARENTHESIS) {
				break;
			}

			if ($tokens[$fixEndPointer + 1]['code'] === T_COMMA && $tokens[$fixStartPointer]['code'] === T_COMMA) {
				break;
			}

			if ($tokens[$fixEndPointer + 1]['code'] === T_VARIABLE) {
				break;
			}

			$fixEndPointer++;
		} while (true);

		$phpcsFile->fixer->beginChangeset();
		for ($i = $fixStartPointer; $i <= $fixEndPointer; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}

		$emptyUse = true;
		for ($i = $useParenthesisOpenerPointer + 1; $i < $useParenthesisCloserPointer; $i++) {
			if ($phpcsFile->fixer->getTokenContent($i) !== '') {
				$emptyUse = false;
				break;
			}
		}
		if ($emptyUse) {
			for ($i = $usePointer; $i <= $useParenthesisCloserPointer; $i++) {
				$phpcsFile->fixer->replaceToken($i, '');
			}
		}

		$phpcsFile->fixer->endChangeset();
	}

}
