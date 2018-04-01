<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\UseStatement;
use SlevomatCodingStandard\Helpers\UseStatementHelper;

class UseSpacingSniff implements \PHP_CodeSniffer\Sniffs\Sniff
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
	 * @return mixed[]
	 */
	public function register(): array
	{
		return [
			T_OPEN_TAG,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $openTagPointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $openTagPointer): void
	{
		$useStatements = array_values(UseStatementHelper::getUseStatements($phpcsFile, $openTagPointer));

		if (count($useStatements) === 0) {
			return;
		}

		$this->checkLinesBeforeFirstUse($phpcsFile, $useStatements[0]);
		$this->checkLinesAfterLastUse($phpcsFile, $useStatements[count($useStatements) - 1]);
		$this->checkLinesBetweenSameTypesOfUse($phpcsFile, $useStatements);
		$this->checkLinesBetweenDifferentTypesOfUse($phpcsFile, $useStatements);
	}

	private function checkLinesBeforeFirstUse(\PHP_CodeSniffer\Files\File $phpcsFile, UseStatement $firstUse): void
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

	private function checkLinesAfterLastUse(\PHP_CodeSniffer\Files\File $phpcsFile, UseStatement $lastUse): void
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
	private function checkLinesBetweenSameTypesOfUse(\PHP_CodeSniffer\Files\File $phpcsFile, array $useStatements): void
	{
		if (count($useStatements) === 1) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		$requiredLinesCountBetweenUses = 0;

		$previousUse = null;
		foreach ($useStatements as $no => $use) {
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
	private function checkLinesBetweenDifferentTypesOfUse(\PHP_CodeSniffer\Files\File $phpcsFile, array $useStatements): void
	{
		if (count($useStatements) === 1) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		$requiredLinesCountBetweenUseTypes = SniffSettingsHelper::normalizeInteger($this->linesCountBetweenUseTypes);

		$previousUse = null;
		foreach ($useStatements as $no => $use) {
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
