<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function array_key_exists;
use const T_BREAK;
use const T_CLOSE_CURLY_BRACKET;
use const T_CONTINUE;
use const T_DO;
use const T_EQUAL;
use const T_FOR;
use const T_FOREACH;
use const T_GOTO;
use const T_IF;
use const T_RETURN;
use const T_SWITCH;
use const T_THROW;
use const T_TRY;
use const T_WHILE;
use const T_YIELD;
use const T_YIELD_FROM;

/**
 * @deprecated Use BlockControlStructureSpacingSniff and JumpStatementsSpacingSniff instead
 * @codeCoverageIgnore
 */
class ControlStructureSpacingSniff extends AbstractControlStructureSpacingSniff
{

	/** @var int */
	public $linesCountAroundControlStructure = 1;

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $controlStructurePointer
	 */
	public function process(File $phpcsFile, $controlStructurePointer): void
	{
		if ($this->isWhilePartOfDo($phpcsFile, $controlStructurePointer)) {
			return;
		}

		if ($this->isYieldWithAssigment($phpcsFile, $controlStructurePointer)) {
			return;
		}

		if ($this->isYieldFromWithReturn($phpcsFile, $controlStructurePointer)) {
			return;
		}

		$this->linesCountBeforeControlStructure = $this->linesCountAfterControlStructure = $this->linesCountAroundControlStructure;

		parent::process($phpcsFile, $controlStructurePointer);
	}

	/**
	 * @return int[]
	 */
	protected function getSupportedTokens(): array
	{
		return [
			T_IF,
			T_DO,
			T_WHILE,
			T_FOR,
			T_FOREACH,
			T_SWITCH,
			T_TRY,
			T_GOTO,
			T_BREAK,
			T_CONTINUE,
			T_RETURN,
			T_THROW,
			T_YIELD,
			T_YIELD_FROM,
		];
	}

	private function isWhilePartOfDo(File $phpcsFile, int $controlStructurePointer): bool
	{
		$tokens = $phpcsFile->getTokens();
		$pointerBefore = TokenHelper::findPreviousEffective($phpcsFile, $controlStructurePointer - 1);

		return
			$tokens[$controlStructurePointer]['code'] === T_WHILE
			&& $tokens[$pointerBefore]['code'] === T_CLOSE_CURLY_BRACKET
			&& array_key_exists('scope_condition', $tokens[$pointerBefore])
			&& $tokens[$tokens[$pointerBefore]['scope_condition']]['code'] === T_DO;
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

}
