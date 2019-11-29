<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function abs;
use function in_array;
use const T_BREAK;
use const T_CONTINUE;
use const T_EQUAL;
use const T_GOTO;
use const T_RETURN;
use const T_THROW;
use const T_YIELD;
use const T_YIELD_FROM;

class JumpStatementsSpacingSniff extends AbstractControlStructureSpacingSniff
{

	/** @var bool */
	public $allowSingleLineYieldStacking = true;

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $controlStructurePointer
	 */
	public function process(File $phpcsFile, $controlStructurePointer): void
	{
		if ($this->isYieldWithAssigment($phpcsFile, $controlStructurePointer)) {
			return;
		}

		if ($this->isYieldFromWithReturn($phpcsFile, $controlStructurePointer)) {
			return;
		}

		parent::process($phpcsFile, $controlStructurePointer);
	}

	/**
	 * @return int[]
	 */
	protected function getSupportedTokens(): array
	{
		return [
			T_GOTO,
			T_BREAK,
			T_CONTINUE,
			T_RETURN,
			T_THROW,
			T_YIELD,
			T_YIELD_FROM,
		];
	}

	protected function checkLinesBefore(File $phpcsFile, int $controlStructurePointer): void
	{
		if ($this->allowSingleLineYieldStacking
			&& $this->isStackedSingleLineYield($phpcsFile, $controlStructurePointer, true)) {
			return;
		}

		parent::checkLinesBefore($phpcsFile, $controlStructurePointer);
	}

	protected function checkLinesAfter(File $phpcsFile, int $controlStructurePointer): void
	{
		if ($this->allowSingleLineYieldStacking
			&& $this->isStackedSingleLineYield($phpcsFile, $controlStructurePointer, false)) {
			return;
		}

		parent::checkLinesAfter($phpcsFile, $controlStructurePointer);
	}

	private function isYieldWithAssigment(File $phpcsFile, int $controlStructurePointer): bool
	{
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$controlStructurePointer]['code'] !== T_YIELD) {
			return false;
		}

		$pointerBefore = TokenHelper::findPreviousEffective($phpcsFile, $controlStructurePointer - 1);

		return $tokens[$pointerBefore]['code'] === T_EQUAL;
	}

	private function isYieldFromWithReturn(File $phpcsFile, int $controlStructurePointer): bool
	{
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$controlStructurePointer]['code'] !== T_YIELD_FROM) {
			return false;
		}

		$pointerBefore = TokenHelper::findPreviousEffective($phpcsFile, $controlStructurePointer - 1);

		return $tokens[$pointerBefore]['code'] === T_RETURN;
	}

	private function isStackedSingleLineYield(File $phpcsFile, int $controlStructureStartPointer, bool $previous): bool
	{
		$tokens = $phpcsFile->getTokens();
		$yields = [T_YIELD, T_YIELD_FROM];

		if (!in_array($tokens[$controlStructureStartPointer]['code'], $yields, true)) {
			return false;
		}

		$adjoiningYieldPointer = $previous
			? TokenHelper::findPrevious($phpcsFile, $yields, $controlStructureStartPointer - 1)
			: TokenHelper::findNext($phpcsFile, $yields, $controlStructureStartPointer + 1);

		return $adjoiningYieldPointer !== null
			&& abs($tokens[$adjoiningYieldPointer]['line'] - $tokens[$controlStructureStartPointer]['line']) === 1;
	}

}
