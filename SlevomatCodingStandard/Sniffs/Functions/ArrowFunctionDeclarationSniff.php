<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function preg_match;
use function sprintf;
use function str_repeat;
use function strlen;
use function strpos;
use const T_FN;
use const T_FN_ARROW;

class ArrowFunctionDeclarationSniff implements Sniff
{

	public const CODE_INCORRECT_SPACES_AFTER_KEYWORD = 'IncorrectSpacesAfterKeyword';
	public const CODE_INCORRECT_SPACES_BEFORE_ARROW = 'IncorrectSpacesBeforeArrow';
	public const CODE_INCORRECT_SPACES_AFTER_ARROW = 'IncorrectSpacesAfterArrow';

	public int $spacesCountAfterKeyword = 1;

	public int $spacesCountBeforeArrow = 1;

	public int $spacesCountAfterArrow = 1;

	public bool $allowMultiLine = false;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_FN,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $arrowFunctionPointer
	 */
	public function process(File $phpcsFile, $arrowFunctionPointer): void
	{
		$this->spacesCountAfterKeyword = SniffSettingsHelper::normalizeInteger($this->spacesCountAfterKeyword);
		$this->spacesCountBeforeArrow = SniffSettingsHelper::normalizeInteger($this->spacesCountBeforeArrow);
		$this->spacesCountAfterArrow = SniffSettingsHelper::normalizeInteger($this->spacesCountAfterArrow);

		$this->checkSpacesAfterKeyword($phpcsFile, $arrowFunctionPointer);

		$arrowPointer = TokenHelper::findNext($phpcsFile, T_FN_ARROW, $arrowFunctionPointer);

		$this->checkSpacesBeforeArrow($phpcsFile, $arrowPointer);
		$this->checkSpacesAfterArrow($phpcsFile, $arrowPointer);
	}

	private function checkSpacesAfterKeyword(File $phpcsFile, int $arrowFunctionPointer): void
	{
		$pointerAfter = TokenHelper::findNextNonWhitespace($phpcsFile, $arrowFunctionPointer + 1);

		$spaces = TokenHelper::getContent($phpcsFile, $arrowFunctionPointer + 1, $pointerAfter - 1);

		if ($this->allowMultiLine && strpos($spaces, $phpcsFile->eolChar) === 0) {
			return;
		}

		$actualSpaces = strlen($spaces);

		if (
			$actualSpaces === $this->spacesCountAfterKeyword
			&& (
				$this->spacesCountAfterKeyword === 0
				|| preg_match('~^ +$~', $spaces) === 1
			)
		) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			$this->formatErrorMessage('after "fn" keyword', $this->spacesCountAfterKeyword),
			$arrowFunctionPointer,
			self::CODE_INCORRECT_SPACES_AFTER_KEYWORD,
		);
		if (!$fix) {
			return;
		}

		$this->fixSpaces($phpcsFile, $arrowFunctionPointer, $pointerAfter, $this->spacesCountAfterKeyword);
	}

	private function checkSpacesBeforeArrow(File $phpcsFile, int $arrowPointer): void
	{
		$pointerBefore = TokenHelper::findPreviousNonWhitespace($phpcsFile, $arrowPointer - 1);

		$spaces = TokenHelper::getContent($phpcsFile, $pointerBefore + 1, $arrowPointer - 1);

		if ($this->allowMultiLine && strpos($spaces, $phpcsFile->eolChar) === 0) {
			return;
		}

		$actualSpaces = strlen($spaces);

		if (
			$actualSpaces === $this->spacesCountBeforeArrow
			&& (
				$this->spacesCountBeforeArrow === 0
				|| preg_match('~^ +$~', $spaces) === 1
			)
		) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			$this->formatErrorMessage('before =>', $this->spacesCountBeforeArrow),
			$arrowPointer,
			self::CODE_INCORRECT_SPACES_BEFORE_ARROW,
		);
		if (!$fix) {
			return;
		}

		$this->fixSpaces($phpcsFile, $pointerBefore, $arrowPointer, $this->spacesCountBeforeArrow);
	}

	private function checkSpacesAfterArrow(File $phpcsFile, int $arrowPointer): void
	{
		$pointerAfter = TokenHelper::findNextNonWhitespace($phpcsFile, $arrowPointer + 1);

		$spaces = TokenHelper::getContent($phpcsFile, $arrowPointer + 1, $pointerAfter - 1);

		if ($this->allowMultiLine && strpos($spaces, $phpcsFile->eolChar) === 0) {
			return;
		}

		$actualSpaces = strlen($spaces);

		if ($actualSpaces === $this->spacesCountAfterArrow && ($this->spacesCountAfterArrow === 0 || preg_match('~^ +$~', $spaces) === 1)) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			$this->formatErrorMessage('after =>', $this->spacesCountAfterArrow),
			$arrowPointer,
			self::CODE_INCORRECT_SPACES_AFTER_ARROW,
		);
		if (!$fix) {
			return;
		}

		$this->fixSpaces($phpcsFile, $arrowPointer, $pointerAfter, $this->spacesCountAfterArrow);
	}

	private function formatErrorMessage(string $suffix, int $requiredSpaces): string
	{
		return $requiredSpaces === 0
			? sprintf('There must be no whitespace %s.', $suffix)
			: sprintf('There must be exactly %d whitespace%s %s.', $requiredSpaces, $requiredSpaces !== 1 ? 's' : '', $suffix);
	}

	private function fixSpaces(File $phpcsFile, int $pointerBefore, int $pointerAfter, int $requiredSpaces): void
	{
		$phpcsFile->fixer->beginChangeset();

		if ($requiredSpaces > 0) {
			$phpcsFile->fixer->addContent($pointerBefore, str_repeat(' ', $requiredSpaces));
		}

		FixerHelper::removeBetween($phpcsFile, $pointerBefore, $pointerAfter);

		$phpcsFile->fixer->endChangeset();
	}

}
