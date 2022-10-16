<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use SlevomatCodingStandard\Helpers\CommentHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\UseStatement;
use SlevomatCodingStandard\Helpers\UseStatementHelper;
use function array_key_exists;
use function array_values;
use function count;
use function in_array;
use function sprintf;
use const T_DOC_COMMENT_OPEN_TAG;
use const T_OPEN_TAG;
use const T_SEMICOLON;

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
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_OPEN_TAG,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $openTagPointer
	 */
	public function process(File $phpcsFile, $openTagPointer): void
	{
		$this->linesCountBeforeFirstUse = SniffSettingsHelper::normalizeInteger($this->linesCountBeforeFirstUse);
		$this->linesCountBetweenUseTypes = SniffSettingsHelper::normalizeInteger($this->linesCountBetweenUseTypes);
		$this->linesCountAfterLastUse = SniffSettingsHelper::normalizeInteger($this->linesCountAfterLastUse);

		if (TokenHelper::findPrevious($phpcsFile, T_OPEN_TAG, $openTagPointer - 1) !== null) {
			return;
		}

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
		$pointerBeforeFirstUse = TokenHelper::findPreviousNonWhitespace($phpcsFile, $firstUse->getPointer() - 1);
		$useStartPointer = $firstUse->getPointer();

		if (
			in_array($tokens[$pointerBeforeFirstUse]['code'], Tokens::$commentTokens, true)
			&& $tokens[$pointerBeforeFirstUse]['line'] + 1 === $tokens[$useStartPointer]['line']
		) {
			$useStartPointer = array_key_exists('comment_opener', $tokens[$pointerBeforeFirstUse])
				? $tokens[$pointerBeforeFirstUse]['comment_opener']
				: CommentHelper::getMultilineCommentStartPointer($phpcsFile, $pointerBeforeFirstUse);
			/** @var int $pointerBeforeFirstUse */
			$pointerBeforeFirstUse = TokenHelper::findPreviousNonWhitespace($phpcsFile, $useStartPointer - 1);
		}

		$actualLinesCountBeforeFirstUse = $tokens[$useStartPointer]['line'] - $tokens[$pointerBeforeFirstUse]['line'] - 1;

		if ($actualLinesCountBeforeFirstUse === $this->linesCountBeforeFirstUse) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf(
				'Expected %d line%s before first use statement, found %d.',
				$this->linesCountBeforeFirstUse,
				$this->linesCountBeforeFirstUse === 1 ? '' : 's',
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

		FixerHelper::removeBetween($phpcsFile, $pointerBeforeFirstUse, $useStartPointer);

		for ($i = 0; $i <= $this->linesCountBeforeFirstUse; $i++) {
			$phpcsFile->fixer->addNewline($pointerBeforeFirstUse);
		}
		$phpcsFile->fixer->endChangeset();
	}

	private function checkLinesAfterLastUse(File $phpcsFile, UseStatement $lastUse): void
	{
		$tokens = $phpcsFile->getTokens();

		/** @var int $useEndPointer */
		$useEndPointer = TokenHelper::findNextLocal($phpcsFile, T_SEMICOLON, $lastUse->getPointer() + 1);

		$pointerAfterWhitespaceEnd = TokenHelper::findNextNonWhitespace($phpcsFile, $useEndPointer + 1);
		if ($pointerAfterWhitespaceEnd === null) {
			return;
		}

		if (
			in_array($tokens[$pointerAfterWhitespaceEnd]['code'], Tokens::$commentTokens, true)
			&& $tokens[$pointerAfterWhitespaceEnd]['code'] !== T_DOC_COMMENT_OPEN_TAG
			&& (
				$tokens[$useEndPointer]['line'] === $tokens[$pointerAfterWhitespaceEnd]['line']
				|| $tokens[$useEndPointer]['line'] + 1 === $tokens[$pointerAfterWhitespaceEnd]['line']
			)
		) {
			$useEndPointer = CommentHelper::getMultilineCommentEndPointer($phpcsFile, $pointerAfterWhitespaceEnd);
			/** @var int $pointerAfterWhitespaceEnd */
			$pointerAfterWhitespaceEnd = TokenHelper::findNextNonWhitespace($phpcsFile, $useEndPointer + 1);
		}

		$actualLinesCountAfterLastUse = $tokens[$pointerAfterWhitespaceEnd]['line'] - $tokens[$useEndPointer]['line'] - 1;

		if ($actualLinesCountAfterLastUse === $this->linesCountAfterLastUse) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf(
				'Expected %d line%s after last use statement, found %d.',
				$this->linesCountAfterLastUse,
				$this->linesCountAfterLastUse === 1 ? '' : 's',
				$actualLinesCountAfterLastUse
			),
			$lastUse->getPointer(),
			self::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_USE
		);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		FixerHelper::removeBetween($phpcsFile, $useEndPointer, $pointerAfterWhitespaceEnd);

		$linesToAdd = $this->linesCountAfterLastUse;
		if (CommentHelper::isLineComment($phpcsFile, $useEndPointer)) {
			$linesToAdd--;
		}

		for ($i = 0; $i <= $linesToAdd; $i++) {
			$phpcsFile->fixer->addNewline($useEndPointer);
		}
		$phpcsFile->fixer->endChangeset();
	}

	/**
	 * @param UseStatement[] $useStatements
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

			/** @var int $pointerBeforeUse */
			$pointerBeforeUse = TokenHelper::findPreviousNonWhitespace($phpcsFile, $use->getPointer() - 1);
			$useStartPointer = $use->getPointer();

			if (
				in_array($tokens[$pointerBeforeUse]['code'], Tokens::$commentTokens, true)
				&& TokenHelper::findFirstNonWhitespaceOnLine($phpcsFile, $pointerBeforeUse) === $pointerBeforeUse
				&& $tokens[$pointerBeforeUse]['line'] + 1 === $tokens[$useStartPointer]['line']
			) {
				$useStartPointer = array_key_exists('comment_opener', $tokens[$pointerBeforeUse])
					? $tokens[$pointerBeforeUse]['comment_opener']
					: CommentHelper::getMultilineCommentStartPointer($phpcsFile, $pointerBeforeUse);
			}

			$actualLinesCountAfterPreviousUse = $tokens[$useStartPointer]['line'] - $tokens[$previousUse->getPointer()]['line'] - 1;

			if ($actualLinesCountAfterPreviousUse === $requiredLinesCountBetweenUses) {
				$previousUse = $use;
				continue;
			}

			$fix = $phpcsFile->addFixableError(
				sprintf(
					'Expected 0 lines between same types of use statement, found %d.',
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
			FixerHelper::removeBetween($phpcsFile, $previousUseSemicolonPointer, $useStartPointer);
			$phpcsFile->fixer->addNewline($previousUseSemicolonPointer);
			$phpcsFile->fixer->endChangeset();

			$previousUse = $use;
		}
	}

	/**
	 * @param UseStatement[] $useStatements
	 */
	private function checkLinesBetweenDifferentTypesOfUse(File $phpcsFile, array $useStatements): void
	{
		if (count($useStatements) === 1) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

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

			/** @var int $pointerBeforeUse */
			$pointerBeforeUse = TokenHelper::findPreviousNonWhitespace($phpcsFile, $use->getPointer() - 1);
			$useStartPointer = $use->getPointer();

			if (
				in_array($tokens[$pointerBeforeUse]['code'], Tokens::$commentTokens, true)
				&& TokenHelper::findFirstNonWhitespaceOnLine($phpcsFile, $pointerBeforeUse) === $pointerBeforeUse
				&& $tokens[$pointerBeforeUse]['line'] + 1 === $tokens[$useStartPointer]['line']
			) {
				$useStartPointer = array_key_exists('comment_opener', $tokens[$pointerBeforeUse])
					? $tokens[$pointerBeforeUse]['comment_opener']
					: CommentHelper::getMultilineCommentStartPointer($phpcsFile, $pointerBeforeUse);
			}

			$actualLinesCountAfterPreviousUse = $tokens[$useStartPointer]['line'] - $tokens[$previousUse->getPointer()]['line'] - 1;

			if ($actualLinesCountAfterPreviousUse === $this->linesCountBetweenUseTypes) {
				$previousUse = $use;
				continue;
			}

			$fix = $phpcsFile->addFixableError(
				sprintf(
					'Expected %d line%s between different types of use statement, found %d.',
					$this->linesCountBetweenUseTypes,
					$this->linesCountBetweenUseTypes === 1 ? '' : 's',
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

			FixerHelper::removeBetween($phpcsFile, $previousUseSemicolonPointer, $useStartPointer);

			for ($i = 0; $i <= $this->linesCountBetweenUseTypes; $i++) {
				$phpcsFile->fixer->addNewline($previousUseSemicolonPointer);
			}
			$phpcsFile->fixer->endChangeset();

			$previousUse = $use;
		}
	}

}
