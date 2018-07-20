<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\ClassHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use const T_ANON_CLASS;
use const T_CLASS;
use const T_SEMICOLON;
use const T_TRAIT;
use const T_WHITESPACE;
use function count;
use function sprintf;
use function substr_count;

class TraitUseSpacingSniff implements Sniff
{

	public const CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_USE = 'IncorrectLinesCountBeforeFirstUse';
	public const CODE_INCORRECT_LINES_COUNT_BETWEEN_USES = 'IncorrectLinesCountBetweenUses';
	public const CODE_INCORRECT_LINES_COUNT_AFTER_LAST_USE = 'IncorrectLinesCountAfterLastUse';

	/** @var int */
	public $linesCountBeforeFirstUse = 1;

	/** @var int */
	public $linesCountBetweenUses = 0;

	/** @var int */
	public $linesCountAfterLastUse = 1;

	/**
	 * @return mixed[]
	 */
	public function register(): array
	{
		return [
			T_CLASS,
			T_ANON_CLASS,
			T_TRAIT,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $classPointer
	 */
	public function process(File $phpcsFile, $classPointer): void
	{
		$usePointers = ClassHelper::getTraitUsePointers($phpcsFile, $classPointer);

		if (count($usePointers) === 0) {
			return;
		}

		$this->checkLinesBeforeFirstUse($phpcsFile, $usePointers[0]);
		$this->checkLinesAfterLastUse($phpcsFile, $usePointers[count($usePointers) - 1]);
		$this->checkLinesBetweenUses($phpcsFile, $usePointers);
	}

	private function checkLinesBeforeFirstUse(File $phpcsFile, int $firstUsePointer): void
	{
		/** @var int $pointerBeforeFirstUse */
		$pointerBeforeFirstUse = TokenHelper::findPreviousExcluding($phpcsFile, T_WHITESPACE, $firstUsePointer - 1);

		$whitespaceBeforeFirstUse = '';

		if ($pointerBeforeFirstUse + 1 !== $firstUsePointer) {
			$whitespaceBeforeFirstUse .= TokenHelper::getContent($phpcsFile, $pointerBeforeFirstUse + 1, $firstUsePointer - 1);
		}

		$requiredLinesCountBeforeFirstUse = SniffSettingsHelper::normalizeInteger($this->linesCountBeforeFirstUse);
		$actualLinesCountBeforeFirstUse = substr_count($whitespaceBeforeFirstUse, $phpcsFile->eolChar) - 1;

		if ($actualLinesCountBeforeFirstUse === $requiredLinesCountBeforeFirstUse) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf(
				'Expected %d lines before first use statement, found %d.',
				$requiredLinesCountBeforeFirstUse,
				$actualLinesCountBeforeFirstUse
			),
			$firstUsePointer,
			self::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_USE
		);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		$pointerBeforeIndentation = TokenHelper::findPreviousContent($phpcsFile, T_WHITESPACE, $phpcsFile->eolChar, $firstUsePointer, $pointerBeforeFirstUse);
		if ($pointerBeforeIndentation !== null) {
			for ($i = $pointerBeforeFirstUse + 1; $i <= $pointerBeforeIndentation; $i++) {
				$phpcsFile->fixer->replaceToken($i, '');
			}
		}
		for ($i = 0; $i <= $requiredLinesCountBeforeFirstUse; $i++) {
			$phpcsFile->fixer->addNewline($pointerBeforeFirstUse);
		}
		$phpcsFile->fixer->endChangeset();
	}

	private function checkLinesAfterLastUse(File $phpcsFile, int $lastUsePointer): void
	{
		/** @var int $lastUseSemicolonPointer */
		$lastUseSemicolonPointer = TokenHelper::findNextLocal($phpcsFile, T_SEMICOLON, $lastUsePointer + 1);

		$pointerAfterWhitespaceEnd = TokenHelper::findNextExcluding($phpcsFile, T_WHITESPACE, $lastUseSemicolonPointer + 1);
		$whitespaceAfterLastUse = TokenHelper::getContent($phpcsFile, $lastUseSemicolonPointer + 1, $pointerAfterWhitespaceEnd - 1);

		$requiredLinesCountAfterLastUse = SniffSettingsHelper::normalizeInteger($this->linesCountAfterLastUse);
		$actualLinesCountAfterLastUse = substr_count($whitespaceAfterLastUse, $phpcsFile->eolChar) - 1;

		if ($actualLinesCountAfterLastUse === $requiredLinesCountAfterLastUse) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf(
				'Expected %d lines after last use statement, found %d.',
				$requiredLinesCountAfterLastUse,
				$actualLinesCountAfterLastUse
			),
			$lastUsePointer,
			self::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_USE
		);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();
		for ($i = $lastUseSemicolonPointer + 1; $i < $pointerAfterWhitespaceEnd; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}
		for ($i = 0; $i <= $requiredLinesCountAfterLastUse; $i++) {
			$phpcsFile->fixer->addNewline($lastUseSemicolonPointer);
		}
		$phpcsFile->fixer->endChangeset();
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int[] $usePointers
	 */
	private function checkLinesBetweenUses(File $phpcsFile, array $usePointers): void
	{
		if (count($usePointers) === 1) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		$requiredLinesCountBetweenUses = SniffSettingsHelper::normalizeInteger($this->linesCountBetweenUses);

		$previousUsePointer = null;
		foreach ($usePointers as $usePointer) {
			if ($previousUsePointer === null) {
				$previousUsePointer = $usePointer;
				continue;
			}

			$actualLinesCountAfterPreviousUse = $tokens[$usePointer]['line'] - $tokens[$previousUsePointer]['line'] - 1;

			if ($actualLinesCountAfterPreviousUse === $requiredLinesCountBetweenUses) {
				$previousUsePointer = $usePointer;
				continue;
			}

			$fix = $phpcsFile->addFixableError(
				sprintf(
					'Expected %d lines between same types of use statement, found %d.',
					$requiredLinesCountBetweenUses,
					$actualLinesCountAfterPreviousUse
				),
				$usePointer,
				self::CODE_INCORRECT_LINES_COUNT_BETWEEN_USES
			);

			if (!$fix) {
				$previousUsePointer = $usePointer;
				continue;
			}

			/** @var int $previousUseSemicolonPointer */
			$previousUseSemicolonPointer = TokenHelper::findNextLocal($phpcsFile, T_SEMICOLON, $previousUsePointer + 1);
			$pointerBeforeIndentation = TokenHelper::findPreviousContent($phpcsFile, T_WHITESPACE, $phpcsFile->eolChar, $usePointer, $previousUseSemicolonPointer);

			$phpcsFile->fixer->beginChangeset();
			if ($pointerBeforeIndentation !== null) {
				for ($i = $previousUseSemicolonPointer + 1; $i <= $pointerBeforeIndentation; $i++) {
					$phpcsFile->fixer->replaceToken($i, '');
				}
			}
			for ($i = 0; $i <= $requiredLinesCountBetweenUses; $i++) {
				$phpcsFile->fixer->addNewline($previousUseSemicolonPointer);
			}
			$phpcsFile->fixer->endChangeset();

			$previousUsePointer = $usePointer;
		}
	}

}
