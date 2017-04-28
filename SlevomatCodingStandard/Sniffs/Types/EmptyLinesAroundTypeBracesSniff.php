<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Types;

use SlevomatCodingStandard\Helpers\SniffSettingsHelper;

class EmptyLinesAroundTypeBracesSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	const CODE_NO_EMPTY_LINE_AFTER_OPENING_BRACE = 'NoEmptyLineAfterOpeningBrace';

	const CODE_MULTIPLE_EMPTY_LINES_AFTER_OPENING_BRACE = 'MultipleEmptyLinesAfterOpeningBrace';

	const CODE_INCORRECT_EMPTY_LINES_AFTER_OPENING_BRACE = 'IncorrectEmptyLinesAfterOpeningBrace';

	const CODE_NO_EMPTY_LINE_BEFORE_CLOSING_BRACE = 'NoEmptyLineBeforeClosingBrace';

	const CODE_MULTIPLE_EMPTY_LINES_BEFORE_CLOSING_BRACE = 'MultipleEmptyLinesBeforeClosingBrace';

	const CODE_INCORRECT_EMPTY_LINES_BEFORE_CLOSING_BRACE = 'IncorrectEmptyLinesBeforeClosingBrace';

	/** @var int */
	public $linesCountAfterOpeningBrace = 1;

	/** @var int */
	public $linesCountBeforeClosingBrace = 1;

	/**
	 * @return int[]
	 */
	public function register(): array
	{
		return [
			T_CLASS,
			T_INTERFACE,
			T_TRAIT,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $stackPointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $stackPointer)
	{
		$this->processOpeningBrace($phpcsFile, $stackPointer);
		$this->processClosingBrace($phpcsFile, $stackPointer);
	}

	private function processOpeningBrace(\PHP_CodeSniffer\Files\File $phpcsFile, int $stackPointer)
	{
		$tokens = $phpcsFile->getTokens();
		$typeToken = $tokens[$stackPointer];
		$openerPointer = $typeToken['scope_opener'];
		$openerToken = $tokens[$openerPointer];
		$nextPointerAfterOpeningBrace = $phpcsFile->findNext(T_WHITESPACE, $openerPointer + 1, null, true);
		$nextTokenAfterOpeningBrace = $tokens[$nextPointerAfterOpeningBrace];
		$linesCountAfterOpeningBrace = SniffSettingsHelper::normalizeInteger($this->linesCountAfterOpeningBrace);
		$lines = $nextTokenAfterOpeningBrace['line'] - $openerToken['line'] - 1;

		if ($lines === $linesCountAfterOpeningBrace) {
			return;
		}

		if ($linesCountAfterOpeningBrace === 1) {
			if ($lines === 0) {
				$fix = $phpcsFile->addFixableError(sprintf(
					'There must be one empty line after %s opening brace.',
					$typeToken['content']
				), $openerPointer, self::CODE_NO_EMPTY_LINE_AFTER_OPENING_BRACE);
			} else {
				$fix = $phpcsFile->addFixableError(sprintf(
					'There must be one empty line after %s opening brace.',
					$typeToken['content']
				), $openerPointer, self::CODE_MULTIPLE_EMPTY_LINES_AFTER_OPENING_BRACE);
			}
		} else {
			$fix = $phpcsFile->addFixableError(sprintf(
				'There must be exactly %d empty lines after %s opening brace.',
				$linesCountAfterOpeningBrace,
				$typeToken['content']
			), $openerPointer, self::CODE_INCORRECT_EMPTY_LINES_AFTER_OPENING_BRACE);
		}

		if (!$fix) {
			return;
		}

		if ($lines < $linesCountAfterOpeningBrace) {
			$phpcsFile->fixer->beginChangeset();
			for ($i = $lines; $i < $linesCountAfterOpeningBrace; $i++) {
				$phpcsFile->fixer->addNewline($openerPointer);
			}
			$phpcsFile->fixer->endChangeset();
		} else {
			$phpcsFile->fixer->beginChangeset();
			for ($i = $openerPointer + $linesCountAfterOpeningBrace + 2; $i < $nextPointerAfterOpeningBrace; $i++) {
				if ($tokens[$i]['content'] !== $phpcsFile->eolChar) {
					break;
				}
				$phpcsFile->fixer->replaceToken($i, '');
			}
			$phpcsFile->fixer->endChangeset();
		}
	}

	private function processClosingBrace(\PHP_CodeSniffer\Files\File $phpcsFile, int $stackPointer)
	{
		$tokens = $phpcsFile->getTokens();
		$typeToken = $tokens[$stackPointer];
		$closerPointer = $typeToken['scope_closer'];
		$closerToken = $tokens[$closerPointer];
		$previousPointerBeforeClosingBrace = $phpcsFile->findPrevious(T_WHITESPACE, $closerPointer - 1, null, true);
		$previousTokenBeforeClosingBrace = $tokens[$previousPointerBeforeClosingBrace];
		$linesCountBeforeClosingBrace = SniffSettingsHelper::normalizeInteger($this->linesCountBeforeClosingBrace);
		$lines = $closerToken['line'] - $previousTokenBeforeClosingBrace['line'] - 1;

		if ($lines === $linesCountBeforeClosingBrace) {
			return;
		}

		if ($linesCountBeforeClosingBrace === 1) {
			if ($lines === 0) {
				$fix = $phpcsFile->addFixableError(sprintf(
					'There must be one empty line before %s closing brace.',
					$typeToken['content']
				), $closerPointer, self::CODE_NO_EMPTY_LINE_BEFORE_CLOSING_BRACE);
			} else {
				$fix = $phpcsFile->addFixableError(sprintf(
					'There must be one empty line before %s closing brace.',
					$typeToken['content']
				), $closerPointer, self::CODE_MULTIPLE_EMPTY_LINES_BEFORE_CLOSING_BRACE);
			}
		} else {
			$fix = $phpcsFile->addFixableError(sprintf(
				'There must be exactly %d empty lines before %s closing brace.',
				$linesCountBeforeClosingBrace,
				$typeToken['content']
			), $closerPointer, self::CODE_INCORRECT_EMPTY_LINES_BEFORE_CLOSING_BRACE);
		}

		if (!$fix) {
			return;
		}

		if ($lines < $linesCountBeforeClosingBrace) {
			$phpcsFile->fixer->beginChangeset();
			for ($i = $lines; $i < $linesCountBeforeClosingBrace; $i++) {
				$phpcsFile->fixer->addNewlineBefore($closerPointer);
			}
			$phpcsFile->fixer->endChangeset();
		} else {
			$phpcsFile->fixer->beginChangeset();
			for ($i = $previousPointerBeforeClosingBrace + $linesCountBeforeClosingBrace + 2; $i < $closerPointer; $i++) {
				$phpcsFile->fixer->replaceToken($i, '');
			}
			$phpcsFile->fixer->endChangeset();
		}
	}

}
