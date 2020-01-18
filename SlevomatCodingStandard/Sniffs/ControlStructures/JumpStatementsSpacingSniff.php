<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function abs;
use function array_key_exists;
use function in_array;
use const T_BREAK;
use const T_CONTINUE;
use const T_GOTO;
use const T_RETURN;
use const T_THROW;
use const T_YIELD;
use const T_YIELD_FROM;

class JumpStatementsSpacingSniff extends AbstractControlStructureSpacing
{

	/** @var int */
	public $linesCountBeforeControlStructure = 1;

	/** @var int */
	public $linesCountBeforeFirstControlStructure = 0;

	/** @var int */
	public $linesCountAfterControlStructure = 1;

	/** @var int */
	public $linesCountAfterLastControlStructure = 0;

	/** @var bool */
	public $allowSingleLineYieldStacking = true;

	/** @var string[] */
	public $tokensToCheck = [];

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param File $phpcsFile
	 * @param int $controlStructurePointer
	 */
	public function process(File $phpcsFile, $controlStructurePointer): void
	{
		if ($this->isOneOfYieldSpecialCases($phpcsFile, $controlStructurePointer)) {
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

	/**
	 * @return string[]
	 */
	protected function getTokensToCheck(): array
	{
		return $this->tokensToCheck;
	}

	protected function getLinesCountBefore(): int
	{
		return SniffSettingsHelper::normalizeInteger($this->linesCountBeforeControlStructure);
	}

	protected function getLinesCountBeforeFirst(): int
	{
		return SniffSettingsHelper::normalizeInteger($this->linesCountBeforeFirstControlStructure);
	}

	protected function getLinesCountAfter(): int
	{
		return SniffSettingsHelper::normalizeInteger($this->linesCountAfterControlStructure);
	}

	protected function getLinesCountAfterLast(): int
	{
		return SniffSettingsHelper::normalizeInteger($this->linesCountAfterLastControlStructure);
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

	private function isOneOfYieldSpecialCases(File $phpcsFile, int $controlStructurePointer): bool
	{
		$tokens = $phpcsFile->getTokens();

		$controlStructure = $tokens[$controlStructurePointer];
		if ($controlStructure['code'] !== T_YIELD && $controlStructure['code'] !== T_YIELD_FROM) {
			return false;
		}

		// check if yield is used inside parentheses (function call, while, ...)
		if (array_key_exists('nested_parenthesis', $controlStructure)) {
			return true;
		}

		$pointerBefore = TokenHelper::findPreviousEffective($phpcsFile, $controlStructurePointer - 1);

		// check if yield is used in assignment
		if (in_array($tokens[$pointerBefore]['code'], Tokens::$assignmentTokens, true)) {
			return true;
		}

		// check if yield is used in a return statement
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
