<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function array_values;
use function sprintf;

class EmptyLinesAroundClassBracesSniff implements Sniff
{

	public const CODE_NO_EMPTY_LINE_AFTER_OPENING_BRACE = 'NoEmptyLineAfterOpeningBrace';

	public const CODE_MULTIPLE_EMPTY_LINES_AFTER_OPENING_BRACE = 'MultipleEmptyLinesAfterOpeningBrace';

	public const CODE_INCORRECT_EMPTY_LINES_AFTER_OPENING_BRACE = 'IncorrectEmptyLinesAfterOpeningBrace';

	public const CODE_NO_EMPTY_LINE_BEFORE_CLOSING_BRACE = 'NoEmptyLineBeforeClosingBrace';

	public const CODE_MULTIPLE_EMPTY_LINES_BEFORE_CLOSING_BRACE = 'MultipleEmptyLinesBeforeClosingBrace';

	public const CODE_INCORRECT_EMPTY_LINES_BEFORE_CLOSING_BRACE = 'IncorrectEmptyLinesBeforeClosingBrace';

	/** @var int */
	public $linesCountAfterOpeningBrace = 1;

	/** @var int */
	public $linesCountBeforeClosingBrace = 1;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return array_values(Tokens::$ooScopeTokens);
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $stackPointer
	 */
	public function process(File $phpcsFile, $stackPointer): void
	{
		$this->linesCountAfterOpeningBrace = SniffSettingsHelper::normalizeInteger($this->linesCountAfterOpeningBrace);
		$this->linesCountBeforeClosingBrace = SniffSettingsHelper::normalizeInteger($this->linesCountBeforeClosingBrace);

		$this->processOpeningBrace($phpcsFile, $stackPointer);
		$this->processClosingBrace($phpcsFile, $stackPointer);
	}

	private function processOpeningBrace(File $phpcsFile, int $stackPointer): void
	{
		$tokens = $phpcsFile->getTokens();
		$typeToken = $tokens[$stackPointer];
		$openerPointer = $typeToken['scope_opener'];
		$openerToken = $tokens[$openerPointer];
		$nextPointerAfterOpeningBrace = TokenHelper::findNextNonWhitespace($phpcsFile, $openerPointer + 1);
		$nextTokenAfterOpeningBrace = $tokens[$nextPointerAfterOpeningBrace];
		$lines = $nextTokenAfterOpeningBrace['line'] - $openerToken['line'] - 1;

		if ($lines === $this->linesCountAfterOpeningBrace) {
			return;
		}

		if ($this->linesCountAfterOpeningBrace === 1) {
			$fix = $phpcsFile->addFixableError(
				sprintf('There must be one empty line after %s opening brace.', $typeToken['content']),
				$openerPointer,
				$lines === 0
					? self::CODE_NO_EMPTY_LINE_AFTER_OPENING_BRACE
					: self::CODE_MULTIPLE_EMPTY_LINES_AFTER_OPENING_BRACE
			);
		} else {
			$fix = $phpcsFile->addFixableError(sprintf(
				'There must be exactly %d empty lines after %s opening brace.',
				$this->linesCountAfterOpeningBrace,
				$typeToken['content']
			), $openerPointer, self::CODE_INCORRECT_EMPTY_LINES_AFTER_OPENING_BRACE);
		}

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		if ($lines < $this->linesCountAfterOpeningBrace) {
			for ($i = $lines; $i < $this->linesCountAfterOpeningBrace; $i++) {
				$phpcsFile->fixer->addNewline($openerPointer);
			}
		} else {
			for ($i = $openerPointer + $this->linesCountAfterOpeningBrace + 2; $i < $nextPointerAfterOpeningBrace; $i++) {
				if ($phpcsFile->fixer->getTokenContent($i) !== $phpcsFile->eolChar) {
					break;
				}
				$phpcsFile->fixer->replaceToken($i, '');
			}
		}

		$phpcsFile->fixer->endChangeset();
	}

	private function processClosingBrace(File $phpcsFile, int $stackPointer): void
	{
		$tokens = $phpcsFile->getTokens();
		$typeToken = $tokens[$stackPointer];
		$closerPointer = $typeToken['scope_closer'];
		$closerToken = $tokens[$closerPointer];
		$previousPointerBeforeClosingBrace = TokenHelper::findPreviousNonWhitespace($phpcsFile, $closerPointer - 1);
		$previousTokenBeforeClosingBrace = $tokens[$previousPointerBeforeClosingBrace];
		$lines = $closerToken['line'] - $previousTokenBeforeClosingBrace['line'] - 1;

		if ($lines === $this->linesCountBeforeClosingBrace) {
			return;
		}

		if ($this->linesCountBeforeClosingBrace === 1) {
			$fix = $phpcsFile->addFixableError(
				sprintf('There must be one empty line before %s closing brace.', $typeToken['content']),
				$closerPointer,
				$lines === 0
					? self::CODE_NO_EMPTY_LINE_BEFORE_CLOSING_BRACE
					: self::CODE_MULTIPLE_EMPTY_LINES_BEFORE_CLOSING_BRACE
			);
		} else {
			$fix = $phpcsFile->addFixableError(sprintf(
				'There must be exactly %d empty lines before %s closing brace.',
				$this->linesCountBeforeClosingBrace,
				$typeToken['content']
			), $closerPointer, self::CODE_INCORRECT_EMPTY_LINES_BEFORE_CLOSING_BRACE);
		}

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		if ($lines < $this->linesCountBeforeClosingBrace) {
			for ($i = $lines; $i < $this->linesCountBeforeClosingBrace; $i++) {
				$phpcsFile->fixer->addNewlineBefore($closerPointer);
			}
		} else {
			FixerHelper::removeBetween(
				$phpcsFile,
				$previousPointerBeforeClosingBrace + $this->linesCountBeforeClosingBrace + 1,
				$closerPointer
			);
		}

		$phpcsFile->fixer->endChangeset();
	}

}
