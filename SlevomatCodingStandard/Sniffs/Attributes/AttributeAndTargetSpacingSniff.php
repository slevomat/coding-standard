<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Attributes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\AttributeHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\IndentationHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function sprintf;
use function str_repeat;
use const T_ATTRIBUTE;
use const T_COMMENT;

class AttributeAndTargetSpacingSniff implements Sniff
{

	public const CODE_INCORRECT_LINES_COUNT_BETWEEN_ATTRIBUTE_AND_TARGET = 'IncorrectLinesCountBetweenAttributeAndTarget';

	public int $linesCount = 0;

	public bool $allowOnSameLine = false;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [T_ATTRIBUTE];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $attributeOpenerPointer
	 */
	public function process(File $phpcsFile, $attributeOpenerPointer): void
	{
		$this->linesCount = SniffSettingsHelper::normalizeInteger($this->linesCount);

		if (!AttributeHelper::isValidAttribute($phpcsFile, $attributeOpenerPointer)) {
			return;
		}

		$tokens = $phpcsFile->getTokens();
		$attributeCloserPointer = $tokens[$attributeOpenerPointer]['attribute_closer'];

		$pointerAfter = TokenHelper::findNextNonWhitespace($phpcsFile, $attributeCloserPointer + 1);

		while ($tokens[$pointerAfter]['code'] === T_COMMENT) {
			$pointerAfter = TokenHelper::findNextNonWhitespace($phpcsFile, $pointerAfter + 1);
		}

		if ($tokens[$pointerAfter]['code'] === T_ATTRIBUTE) {
			return;
		}

		$areOnSameLine = $tokens[$pointerAfter]['line'] === $tokens[$attributeCloserPointer]['line'];

		if ($areOnSameLine) {
			if ($this->allowOnSameLine) {
				return;
			}

			$errorMessage = $this->linesCount === 1
				? 'Expected 1 blank line between attribute and its target, both are on same line.'
				: sprintf('Expected %1$d blank lines between attribute and its target, both are on same line.', $this->linesCount);
		} else {
			$actualLinesCount = $tokens[$pointerAfter]['line'] - $tokens[$attributeCloserPointer]['line'] - 1;

			if ($this->linesCount === $actualLinesCount) {
				return;
			}

			$errorMessage = $this->linesCount === 1
				? sprintf('Expected 1 blank line between attribute and its target, found %1$d.', $actualLinesCount)
				: sprintf('Expected %1$d blank lines between attribute and its target, found %2$d.', $this->linesCount, $actualLinesCount);
		}

		$fix = $phpcsFile->addFixableError(
			$errorMessage,
			$attributeOpenerPointer,
			self::CODE_INCORRECT_LINES_COUNT_BETWEEN_ATTRIBUTE_AND_TARGET,
		);

		if (!$fix) {
			return;
		}

		if ($areOnSameLine) {
			$indentation = IndentationHelper::getIndentation(
				$phpcsFile,
				TokenHelper::findFirstNonWhitespaceOnLine($phpcsFile, $pointerAfter),
			);

			$phpcsFile->fixer->beginChangeset();

			FixerHelper::removeWhitespaceAfter($phpcsFile, $attributeCloserPointer);
			$phpcsFile->fixer->addContentBefore($pointerAfter, str_repeat($phpcsFile->eolChar, $this->linesCount + 1) . $indentation);

			$phpcsFile->fixer->endChangeset();

			return;
		}

		$firstTokenOnLine = TokenHelper::findFirstTokenOnLine($phpcsFile, $pointerAfter);

		$phpcsFile->fixer->beginChangeset();

		FixerHelper::removeBetween($phpcsFile, $attributeCloserPointer, $firstTokenOnLine);

		$phpcsFile->fixer->addContentBefore($firstTokenOnLine, str_repeat($phpcsFile->eolChar, $this->linesCount + 1));

		$phpcsFile->fixer->endChangeset();
	}

}
