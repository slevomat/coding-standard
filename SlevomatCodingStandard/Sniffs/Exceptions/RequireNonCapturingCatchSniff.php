<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Exceptions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\CatchHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\ParameterHelper;
use SlevomatCodingStandard\Helpers\ScopeHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\VariableHelper;
use function array_reverse;
use function count;
use function in_array;
use const T_CATCH;
use const T_DOUBLE_QUOTED_STRING;
use const T_FINALLY;
use const T_HEREDOC;
use const T_VARIABLE;
use const T_WHITESPACE;

class RequireNonCapturingCatchSniff implements Sniff
{

	public const CODE_NON_CAPTURING_CATCH_REQUIRED = 'NonCapturingCatchRequired';

	public ?bool $enable = null;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_CATCH,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $catchPointer
	 */
	public function process(File $phpcsFile, $catchPointer): void
	{
		$this->enable = SniffSettingsHelper::isEnabledByPhpVersion($this->enable, 80000);

		if (!$this->enable) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		$variablePointer = TokenHelper::findNext(
			$phpcsFile,
			T_VARIABLE,
			$tokens[$catchPointer]['parenthesis_opener'],
			$tokens[$catchPointer]['parenthesis_closer'],
		);
		if ($variablePointer === null) {
			return;
		}

		$variableName = $tokens[$variablePointer]['content'];

		if ($this->isVariableUsedInCodePart(
			$phpcsFile,
			$tokens[$catchPointer]['scope_opener'],
			$tokens[$catchPointer]['scope_closer'],
			$variableName,
		)) {
			return;
		}

		$tryEndPointer = CatchHelper::getTryEndPointer($phpcsFile, $catchPointer);

		$possibleFinallyPointer = $tokens[$tryEndPointer]['scope_condition'];
		if (
			$tokens[$possibleFinallyPointer]['code'] === T_FINALLY
			&& $this->isVariableUsedInCodePart(
				$phpcsFile,
				$tokens[$possibleFinallyPointer]['scope_opener'],
				$tokens[$possibleFinallyPointer]['scope_closer'],
				$variableName,
			)
		) {
			return;
		}

		$nextScopeEnd = count($tokens) - 1;

		foreach (array_reverse($tokens[$tryEndPointer]['conditions'], true) as $conditionPointer => $conditionCode) {
			if (in_array($conditionCode, TokenHelper::$functionTokenCodes, true)) {
				$nextScopeEnd = $tokens[$conditionPointer]['scope_closer'];
				break;
			}
		}

		if ($this->isVariableUsedInCodePart($phpcsFile, $tryEndPointer, $nextScopeEnd, $variableName)) {
			return;
		}

		$fix = $phpcsFile->addFixableError('Non-capturing catch is required.', $catchPointer, self::CODE_NON_CAPTURING_CATCH_REQUIRED);

		if (!$fix) {
			return;
		}

		$pointerBeforeVariable = TokenHelper::findPreviousEffective($phpcsFile, $variablePointer - 1);
		$fixEndPointer = TokenHelper::findNextContent(
			$phpcsFile,
			T_WHITESPACE,
			$phpcsFile->eolChar,
			$variablePointer + 1,
			$tokens[$catchPointer]['parenthesis_closer'],
		);
		if ($fixEndPointer === null) {
			$fixEndPointer = $tokens[$catchPointer]['parenthesis_closer'];
		}

		$phpcsFile->fixer->beginChangeset();

		FixerHelper::removeBetween($phpcsFile, $pointerBeforeVariable, $fixEndPointer);

		$phpcsFile->fixer->endChangeset();
	}

	private function isVariableUsedInCodePart(File $phpcsFile, int $codeStartPointer, int $codeEndPointer, string $variableName): bool
	{
		$tokens = $phpcsFile->getTokens();

		$firstPointerInCode = $codeStartPointer + 1;

		for ($i = $firstPointerInCode; $i <= $codeEndPointer; $i++) {
			if ($tokens[$i]['code'] === T_VARIABLE) {
				if ($tokens[$i]['content'] !== $variableName) {
					continue;
				}

				if (ParameterHelper::isParameter($phpcsFile, $i)) {
					continue;
				}

				if (!ScopeHelper::isInSameScope($phpcsFile, $firstPointerInCode, $i)) {
					continue;
				}

				$catchPointer = TokenHelper::findPrevious($phpcsFile, T_CATCH, $i - 1, $firstPointerInCode);
				if ($catchPointer === null) {
					return true;
				}

				if ($tokens[$catchPointer]['parenthesis_closer'] < $i) {
					return true;
				}

			} elseif (
				in_array($tokens[$i]['code'], [T_DOUBLE_QUOTED_STRING, T_HEREDOC], true)
				&& VariableHelper::isUsedInScopeInString($phpcsFile, $variableName, $i)
			) {
				return true;
			}
		}

		return false;
	}

}
