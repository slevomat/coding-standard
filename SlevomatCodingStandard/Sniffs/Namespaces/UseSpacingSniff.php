<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\UseStatement;
use SlevomatCodingStandard\Helpers\UseStatementHelper;
use function array_values;
use function count;
use function sprintf;
use function strlen;
use function substr;
use function substr_count;
use const T_OPEN_TAG;
use const T_SEMICOLON;
use const T_WHITESPACE;

class UseSpacingSniff implements Sniff
{

	public const CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_USE = 'IncorrectLinesCountBeforeFirstUse';
	public const CODE_INCORRECT_LINES_COUNT_BETWEEN_SAME_TYPES_OF_USE = 'IncorrectLinesCountBetweenSameTypeOfUse';
	public const CODE_INCORRECT_LINES_COUNT_BETWEEN_DIFFERENT_TYPES_OF_USE = 'IncorrectLinesCountBetweenDifferentTypeOfUse';
	public const CODE_INCORRECT_LINES_COUNT_AFTER_LAST_USE = 'IncorrectLinesCountAfterLastUse';

	/** @var int */
	public $linesCountBeforeFirstUse = 1;

	/** @var int */
	public $linesCountBetweenUseTypes = 0;

	/** @var int */
	public $linesCountAfterLastUse = 1;

	/**
	 * @return (int|string)[]
	 */
	public function register(): array
	{
		return [
			T_OPEN_TAG,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $openTagPointer
	 */
	public function process(File $phpcsFile, $openTagPointer): void
	{
		$fileUseStatements = UseStatementHelper::getFileUseStatements($phpcsFile);

		if (count($fileUseStatements) === 0) {
			return;
		}

		foreach ($fileUseStatements as $useStatementsByName) {
			$useStatements = array_values($useStatementsByName);

			$this->checkLinesBeforeFirstUse($phpcsFile, $useStatements[0]);
			$this->checkLinesAfterLastUse($phpcsFile, $useStatements[count($useStatements) - 1]);
			$this->checkLinesBetweenSameTypesOfUse($phpcsFile, $useStatements);
			$this->checkLinesBetweenDifferentTypesOfUse($phpcsFile, $useStatements);
		}
	}

	private function checkLinesBeforeFirstUse(File $phpcsFile, UseStatement $firstUse): void
	{
		$tokens = $phpcsFile->getTokens();

		/** @var int $pointerBeforeFirstUse */
		$pointerBeforeFirstUse = TokenHelper::findPreviousExcluding($phpcsFile, T_WHITESPACE, $firstUse->getPointer() - 1);

		$whitespaceBeforeFirstUse = '';

		if ($tokens[$pointerBeforeFirstUse]['code'] === T_OPEN_TAG) {
			$whitespaceBeforeFirstUse .= substr($tokens[$pointerBeforeFirstUse]['content'], strlen('<?php'));
		}

		if ($pointerBeforeFirstUse + 1 !== $firstUse->getPointer()) {
			$whitespaceBeforeFirstUse .= TokenHelper::getContent($phpcsFile, $pointerBeforeFirstUse + 1, $firstUse->getPointer() - 1);
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
			$firstUse->getPointer(),
			self::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_USE
		);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		if ($tokens[$pointerBeforeFirstUse]['code'] === T_OPEN_TAG) {
			$phpcsFile->fixer->replaceToken($pointerBeforeFirstUse, '<?php');
		}

		for ($i = $pointerBeforeFirstUse + 1; $i < $firstUse->getPointer(); $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}
		for ($i = 0; $i <= $requiredLinesCountBeforeFirstUse; $i++) {
			$phpcsFile->fixer->addNewline($pointerBeforeFirstUse);
		}
		$phpcsFile->fixer->endChangeset();
	}

	private function checkLinesAfterLastUse(File $phpcsFile, UseStatement $lastUse): void
	{
		/** @var int $lastUseSemicolonPointer */
		$lastUseSemicolonPointer = TokenHelper::findNextLocal($phpcsFile, T_SEMICOLON, $lastUse->getPointer() + 1);

		$pointerAfterWhitespaceEnd = TokenHelper::findNextExcluding($phpcsFile, T_WHITESPACE, $lastUseSemicolonPointer + 1);
		if ($pointerAfterWhitespaceEnd === null) {
			return;
		}

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
			$lastUse->getPointer(),
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
	 * @param \SlevomatCodingStandard\Helpers\UseStatement[] $useStatements
	 */
	private function checkLinesBetweenSameTypesOfUse(File $phpcsFile, array $useStatements): void
	{
		if (count($useStatements) === 1) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		$requiredLinesCountBetweenUses = 0;

		$previousUse = null;
		foreach ($useStatements as $use) {
			if ($previousUse === null) {
				$previousUse = $use;
				continue;
			}

			if (!$use->hasSameType($previousUse)) {
				$previousUse = null;
				continue;
			}

			$actualLinesCountAfterPreviousUse = $tokens[$use->getPointer()]['line'] - $tokens[$previousUse->getPointer()]['line'] - 1;

			if ($actualLinesCountAfterPreviousUse === $requiredLinesCountBetweenUses) {
				$previousUse = $use;
				continue;
			}

			$fix = $phpcsFile->addFixableError(
				sprintf(
					'Expected %d lines between same types of use statement, found %d.',
					$requiredLinesCountBetweenUses,
					$actualLinesCountAfterPreviousUse
				),
				$use->getPointer(),
				self::CODE_INCORRECT_LINES_COUNT_BETWEEN_SAME_TYPES_OF_USE
			);

			if (!$fix) {
				$previousUse = $use;
				continue;
			}

			/** @var int $previousUseSemicolonPointer */
			$previousUseSemicolonPointer = TokenHelper::findNextLocal($phpcsFile, T_SEMICOLON, $previousUse->getPointer() + 1);

			$phpcsFile->fixer->beginChangeset();
			for ($i = $previousUseSemicolonPointer + 1; $i < $use->getPointer(); $i++) {
				$phpcsFile->fixer->replaceToken($i, '');
			}
			$phpcsFile->fixer->addNewline($previousUseSemicolonPointer);
			$phpcsFile->fixer->endChangeset();

			$previousUse = $use;
		}
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param \SlevomatCodingStandard\Helpers\UseStatement[] $useStatements
	 */
	private function checkLinesBetweenDifferentTypesOfUse(File $phpcsFile, array $useStatements): void
	{
		if (count($useStatements) === 1) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		$requiredLinesCountBetweenUseTypes = SniffSettingsHelper::normalizeInteger($this->linesCountBetweenUseTypes);

		$previousUse = null;
		foreach ($useStatements as $use) {
			if ($previousUse === null) {
				$previousUse = $use;
				continue;
			}

			if ($use->hasSameType($previousUse)) {
				$previousUse = $use;
				continue;
			}

			$actualLinesCountAfterPreviousUse = $tokens[$use->getPointer()]['line'] - $tokens[$previousUse->getPointer()]['line'] - 1;

			if ($actualLinesCountAfterPreviousUse === $requiredLinesCountBetweenUseTypes) {
				$previousUse = $use;
				continue;
			}

			$fix = $phpcsFile->addFixableError(
				sprintf(
					'Expected %d lines between different types of use statement, found %d.',
					$requiredLinesCountBetweenUseTypes,
					$actualLinesCountAfterPreviousUse
				),
				$use->getPointer(),
				self::CODE_INCORRECT_LINES_COUNT_BETWEEN_DIFFERENT_TYPES_OF_USE
			);

			if (!$fix) {
				$previousUse = $use;
				continue;
			}

			/** @var int $previousUseSemicolonPointer */
			$previousUseSemicolonPointer = TokenHelper::findNextLocal($phpcsFile, T_SEMICOLON, $previousUse->getPointer() + 1);

			$phpcsFile->fixer->beginChangeset();
			for ($i = $previousUseSemicolonPointer + 1; $i < $use->getPointer(); $i++) {
				$phpcsFile->fixer->replaceToken($i, '');
			}
			for ($i = 0; $i <= $requiredLinesCountBetweenUseTypes; $i++) {
				$phpcsFile->fixer->addNewline($previousUseSemicolonPointer);
			}
			$phpcsFile->fixer->endChangeset();

			$previousUse = $use;
		}
	}

}
