<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function sprintf;
use function str_repeat;
use function strlen;
use const T_COLON;
use const T_ENUM;

class BackedEnumTypeSpacingSniff implements Sniff
{

	public const CODE_INCORRECT_SPACES_BEFORE_COLON = 'IncorrectSpacesBeforeColon';

	public const CODE_INCORRECT_SPACES_BEFORE_TYPE = 'IncorrectSpacesBeforeType';

	/** @var int */
	public $spacesCountBeforeColon = 0;

	/** @var int */
		public $spacesCountBeforeType = 1;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [T_ENUM];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $enumPointer
	 */
	public function process(File $phpcsFile, $enumPointer): void
	{
		$this->spacesCountBeforeColon = SniffSettingsHelper::normalizeInteger($this->spacesCountBeforeColon);
		$this->spacesCountBeforeType = SniffSettingsHelper::normalizeInteger($this->spacesCountBeforeType);

		$tokens = $phpcsFile->getTokens();

		$colonPointer = TokenHelper::findNext($phpcsFile, T_COLON, $enumPointer + 1, $tokens[$enumPointer]['scope_opener']);

		if ($colonPointer === null) {
			return;
		}

		$this->checkSpacesBeforeColon($phpcsFile, $colonPointer);
		$this->checkSpacesBeforeType($phpcsFile, $colonPointer);
	}

	public function checkSpacesBeforeColon(File $phpcsFile, int $colonPointer): void
	{
		$namePointer = TokenHelper::findPreviousEffective($phpcsFile, $colonPointer - 1);

		$whitespace = TokenHelper::getContent($phpcsFile, $namePointer + 1, $colonPointer - 1);

		if ($this->spacesCountBeforeColon === strlen($whitespace)) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			$this->formatErrorMessage('before colon', $this->spacesCountBeforeColon),
			$colonPointer,
			self::CODE_INCORRECT_SPACES_BEFORE_COLON
		);
		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		for ($i = $namePointer + 1; $i < $colonPointer; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}

		$phpcsFile->fixer->addContentBefore($colonPointer, str_repeat(' ', $this->spacesCountBeforeColon));

		$phpcsFile->fixer->endChangeset();
	}

	public function checkSpacesBeforeType(File $phpcsFile, int $colonPointer): void
	{
		$typePointer = TokenHelper::findNextEffective($phpcsFile, $colonPointer + 1);

		$whitespace = TokenHelper::getContent($phpcsFile, $colonPointer + 1, $typePointer - 1);

		if ($this->spacesCountBeforeType === strlen($whitespace)) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			$this->formatErrorMessage('before type', $this->spacesCountBeforeType),
			$typePointer,
			self::CODE_INCORRECT_SPACES_BEFORE_TYPE
		);
		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		for ($i = $colonPointer + 1; $i < $typePointer; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}

		$phpcsFile->fixer->addContentBefore($typePointer, str_repeat(' ', $this->spacesCountBeforeType));

		$phpcsFile->fixer->endChangeset();
	}

	private function formatErrorMessage(string $suffix, int $requiredSpaces): string
	{
		return $requiredSpaces === 0
			? sprintf('There must be no whitespace %s.', $suffix)
			: sprintf('There must be exactly %d whitespace%s %s.', $requiredSpaces, $requiredSpaces !== 1 ? 's' : '', $suffix);
	}

}
