<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use SlevomatCodingStandard\Helpers\ClassHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function count;
use function in_array;
use function sprintf;
use function substr_count;
use const T_ANON_CLASS;
use const T_CLASS;
use const T_CLOSE_CURLY_BRACKET;
use const T_ENUM;
use const T_OPEN_CURLY_BRACKET;
use const T_SEMICOLON;
use const T_TRAIT;
use const T_WHITESPACE;

class TraitUseSpacingSniff implements Sniff
{

	public const CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_USE = 'IncorrectLinesCountBeforeFirstUse';
	public const CODE_INCORRECT_LINES_COUNT_BETWEEN_USES = 'IncorrectLinesCountBetweenUses';
	public const CODE_INCORRECT_LINES_COUNT_AFTER_LAST_USE = 'IncorrectLinesCountAfterLastUse';

	public int $linesCountBeforeFirstUse = 1;

	public ?int $linesCountBeforeFirstUseWhenFirstInClass = null;

	public int $linesCountBetweenUses = 0;

	public ?int $linesCountAfterLastUse = 1;

	public int $linesCountAfterLastUseWhenLastInClass = 1;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_CLASS,
			T_ANON_CLASS,
			T_TRAIT,
			T_ENUM,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $classPointer
	 */
	public function process(File $phpcsFile, $classPointer): void
	{
		$this->linesCountBeforeFirstUse = SniffSettingsHelper::normalizeInteger($this->linesCountBeforeFirstUse);
		$this->linesCountBeforeFirstUseWhenFirstInClass = SniffSettingsHelper::normalizeNullableInteger(
			$this->linesCountBeforeFirstUseWhenFirstInClass,
		);
		$this->linesCountBetweenUses = SniffSettingsHelper::normalizeInteger($this->linesCountBetweenUses);
		$this->linesCountAfterLastUse = SniffSettingsHelper::normalizeNullableInteger($this->linesCountAfterLastUse);
		$this->linesCountAfterLastUseWhenLastInClass = SniffSettingsHelper::normalizeInteger($this->linesCountAfterLastUseWhenLastInClass);

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
		$tokens = $phpcsFile->getTokens();

		$useStartPointer = $firstUsePointer;

		/** @var int $pointerBeforeFirstUse */
		$pointerBeforeFirstUse = TokenHelper::findPreviousNonWhitespace($phpcsFile, $firstUsePointer - 1);

		if (in_array($tokens[$pointerBeforeFirstUse]['code'], Tokens::$commentTokens, true)) {
			$pointerBeforeFirstUse = TokenHelper::findPreviousEffective($phpcsFile, $pointerBeforeFirstUse - 1);
			$useStartPointer = TokenHelper::findNext($phpcsFile, Tokens::$commentTokens, $pointerBeforeFirstUse + 1);
		}

		$isAtTheStartOfClass = $tokens[$pointerBeforeFirstUse]['code'] === T_OPEN_CURLY_BRACKET;

		$whitespaceBeforeFirstUse = '';

		if ($pointerBeforeFirstUse + 1 !== $firstUsePointer) {
			$whitespaceBeforeFirstUse .= TokenHelper::getContent($phpcsFile, $pointerBeforeFirstUse + 1, $useStartPointer - 1);
		}

		$requiredLinesCountBeforeFirstUse = $this->linesCountBeforeFirstUse;
		if (
			$isAtTheStartOfClass
			&& $this->linesCountBeforeFirstUseWhenFirstInClass !== null
		) {
			$requiredLinesCountBeforeFirstUse = $this->linesCountBeforeFirstUseWhenFirstInClass;
		}

		$actualLinesCountBeforeFirstUse = substr_count($whitespaceBeforeFirstUse, $phpcsFile->eolChar) - 1;

		if ($actualLinesCountBeforeFirstUse === $requiredLinesCountBeforeFirstUse) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf(
				'Expected %d line%s before first use statement, found %d.',
				$requiredLinesCountBeforeFirstUse,
				$requiredLinesCountBeforeFirstUse === 1 ? '' : 's',
				$actualLinesCountBeforeFirstUse,
			),
			$firstUsePointer,
			self::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_USE,
		);

		if (!$fix) {
			return;
		}

		$pointerBeforeIndentation = TokenHelper::findPreviousContent(
			$phpcsFile,
			T_WHITESPACE,
			$phpcsFile->eolChar,
			$firstUsePointer,
			$pointerBeforeFirstUse,
		);

		$phpcsFile->fixer->beginChangeset();

		if ($pointerBeforeIndentation !== null) {
			FixerHelper::removeBetweenIncluding($phpcsFile, $pointerBeforeFirstUse + 1, $pointerBeforeIndentation);
		}
		for ($i = 0; $i <= $requiredLinesCountBeforeFirstUse; $i++) {
			$phpcsFile->fixer->addNewline($pointerBeforeFirstUse);
		}

		$phpcsFile->fixer->endChangeset();
	}

	private function checkLinesAfterLastUse(File $phpcsFile, int $lastUsePointer): void
	{
		$tokens = $phpcsFile->getTokens();

		/** @var int $lastUseEndPointer */
		$lastUseEndPointer = TokenHelper::findNextLocal($phpcsFile, [T_SEMICOLON, T_OPEN_CURLY_BRACKET], $lastUsePointer + 1);
		if ($tokens[$lastUseEndPointer]['code'] === T_OPEN_CURLY_BRACKET) {
			$lastUseEndPointer = $tokens[$lastUseEndPointer]['bracket_closer'];
		}

		$pointerAfterLastUse = TokenHelper::findNextEffective($phpcsFile, $lastUseEndPointer + 1);
		$isAtTheEndOfClass = $tokens[$pointerAfterLastUse]['code'] === T_CLOSE_CURLY_BRACKET;

		$whitespaceEnd = TokenHelper::findNextNonWhitespace($phpcsFile, $lastUseEndPointer + 1) - 1;
		if ($lastUseEndPointer !== $whitespaceEnd && $tokens[$whitespaceEnd]['content'] !== $phpcsFile->eolChar) {
			$lastEolPointer = TokenHelper::findPreviousContent(
				$phpcsFile,
				T_WHITESPACE,
				$phpcsFile->eolChar,
				$whitespaceEnd - 1,
				$lastUseEndPointer,
			);
			$whitespaceEnd = $lastEolPointer ?? $lastUseEndPointer;
		}
		$whitespaceAfterLastUse = TokenHelper::getContent($phpcsFile, $lastUseEndPointer + 1, $whitespaceEnd);

		$requiredLinesCountAfterLastUse = $isAtTheEndOfClass ? $this->linesCountAfterLastUseWhenLastInClass : $this->linesCountAfterLastUse;

		if ($requiredLinesCountAfterLastUse === null) {
			return;
		}

		$actualLinesCountAfterLastUse = substr_count($whitespaceAfterLastUse, $phpcsFile->eolChar) - 1;

		if ($actualLinesCountAfterLastUse === $requiredLinesCountAfterLastUse) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf(
				'Expected %d line%s after last use statement, found %d.',
				$requiredLinesCountAfterLastUse,
				$requiredLinesCountAfterLastUse === 1 ? '' : 's',
				$actualLinesCountAfterLastUse,
			),
			$lastUsePointer,
			self::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_USE,
		);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		FixerHelper::removeBetweenIncluding($phpcsFile, $lastUseEndPointer + 1, $whitespaceEnd);

		for ($i = 0; $i <= $requiredLinesCountAfterLastUse; $i++) {
			$phpcsFile->fixer->addNewline($lastUseEndPointer);
		}
		$phpcsFile->fixer->endChangeset();
	}

	/**
	 * @param list<int> $usePointers
	 */
	private function checkLinesBetweenUses(File $phpcsFile, array $usePointers): void
	{
		if (count($usePointers) === 1) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		$previousUsePointer = null;
		foreach ($usePointers as $usePointer) {
			if ($previousUsePointer === null) {
				$previousUsePointer = $usePointer;
				continue;
			}

			/** @var int $previousUseEndPointer */
			$previousUseEndPointer = TokenHelper::findNextLocal($phpcsFile, [T_SEMICOLON, T_OPEN_CURLY_BRACKET], $previousUsePointer + 1);
			if ($tokens[$previousUseEndPointer]['code'] === T_OPEN_CURLY_BRACKET) {
				/** @var int $previousUseEndPointer */
				$previousUseEndPointer = $tokens[$previousUseEndPointer]['bracket_closer'];
			}

			$useStartPointer = $usePointer;
			$pointerBeforeUse = TokenHelper::findPreviousNonWhitespace($phpcsFile, $usePointer - 1);

			if (in_array($tokens[$pointerBeforeUse]['code'], Tokens::$commentTokens, true)) {
				$useStartPointer = TokenHelper::findNext(
					$phpcsFile,
					Tokens::$commentTokens,
					TokenHelper::findPreviousEffective($phpcsFile, $pointerBeforeUse - 1) + 1,
				);
			}

			$actualLinesCountAfterPreviousUse = $tokens[$useStartPointer]['line'] - $tokens[$previousUseEndPointer]['line'] - 1;

			if ($actualLinesCountAfterPreviousUse === $this->linesCountBetweenUses) {
				$previousUsePointer = $usePointer;
				continue;
			}

			$errorParameters = [
				sprintf(
					'Expected %d line%s between same types of use statement, found %d.',
					$this->linesCountBetweenUses,
					$this->linesCountBetweenUses === 1 ? '' : 's',
					$actualLinesCountAfterPreviousUse,
				),
				$usePointer,
				self::CODE_INCORRECT_LINES_COUNT_BETWEEN_USES,
			];

			$pointerBeforeUse = TokenHelper::findPreviousEffective($phpcsFile, $usePointer - 1);

			if ($previousUseEndPointer !== $pointerBeforeUse) {
				$phpcsFile->addError(...$errorParameters);
				$previousUsePointer = $usePointer;
				continue;
			}

			$fix = $phpcsFile->addFixableError(...$errorParameters);

			if (!$fix) {
				$previousUsePointer = $usePointer;
				continue;
			}

			$pointerBeforeIndentation = TokenHelper::findPreviousContent(
				$phpcsFile,
				T_WHITESPACE,
				$phpcsFile->eolChar,
				$usePointer,
				$previousUseEndPointer,
			);

			$phpcsFile->fixer->beginChangeset();
			if ($pointerBeforeIndentation !== null) {
				FixerHelper::removeBetweenIncluding($phpcsFile, $previousUseEndPointer + 1, $pointerBeforeIndentation);
			}
			for ($i = 0; $i <= $this->linesCountBetweenUses; $i++) {
				$phpcsFile->fixer->addNewline($previousUseEndPointer);
			}
			$phpcsFile->fixer->endChangeset();

			$previousUsePointer = $usePointer;
		}
	}

}
