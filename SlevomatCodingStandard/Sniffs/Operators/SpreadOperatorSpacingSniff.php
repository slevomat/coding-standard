<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Operators;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function sprintf;
use function str_repeat;
use function strlen;
use const T_ELLIPSIS;

class SpreadOperatorSpacingSniff implements Sniff
{

	public const CODE_INCORRECT_SPACES_AFTER_OPERATOR = 'IncorrectSpacesAfterOperator';

	public int $spacesCountAfterOperator = 0;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_ELLIPSIS,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $spreadOperatorPointer
	 */
	public function process(File $phpcsFile, $spreadOperatorPointer): void
	{
		$this->spacesCountAfterOperator = SniffSettingsHelper::normalizeInteger($this->spacesCountAfterOperator);

		$pointerAfterWhitespace = TokenHelper::findNextNonWhitespace($phpcsFile, $spreadOperatorPointer + 1);

		$whitespace = TokenHelper::getContent($phpcsFile, $spreadOperatorPointer + 1, $pointerAfterWhitespace - 1);

		if ($this->spacesCountAfterOperator === strlen($whitespace)) {
			return;
		}

		$errorMessage = $this->spacesCountAfterOperator === 0
			? 'There must be no whitespace after spread operator.'
			: sprintf(
				'There must be exactly %d whitespace%s after spread operator.',
				$this->spacesCountAfterOperator,
				$this->spacesCountAfterOperator !== 1 ? 's' : '',
			);

		$fix = $phpcsFile->addFixableError($errorMessage, $spreadOperatorPointer, self::CODE_INCORRECT_SPACES_AFTER_OPERATOR);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		$phpcsFile->fixer->addContent($spreadOperatorPointer, str_repeat(' ', $this->spacesCountAfterOperator));
		FixerHelper::removeBetween($phpcsFile, $spreadOperatorPointer, $pointerAfterWhitespace);

		$phpcsFile->fixer->endChangeset();
	}

}
