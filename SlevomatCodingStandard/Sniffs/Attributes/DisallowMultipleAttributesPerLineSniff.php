<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Attributes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\AttributeHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\IndentationHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use const T_ATTRIBUTE;

class DisallowMultipleAttributesPerLineSniff implements Sniff
{

	public const CODE_DISALLOWED_MULTIPLE_ATTRIBUTES_PER_LINE = 'DisallowedMultipleAttributesPerLine';

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
		if (!AttributeHelper::isValidAttribute($phpcsFile, $attributeOpenerPointer)) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		$attributeCloserPointer = $tokens[$attributeOpenerPointer]['attribute_closer'];

		$nextAttributeOpenerPointer = TokenHelper::findNext($phpcsFile, T_ATTRIBUTE, $attributeCloserPointer + 1);

		if ($nextAttributeOpenerPointer === null) {
			return;
		}

		if ($tokens[$attributeCloserPointer]['line'] !== $tokens[$nextAttributeOpenerPointer]['line']) {
			return;
		}

		$attributeTargetPointer = AttributeHelper::getAttributeTarget($phpcsFile, $attributeOpenerPointer);
		$nextAttributeTargetPointer = AttributeHelper::getAttributeTarget($phpcsFile, $nextAttributeOpenerPointer);

		if ($attributeTargetPointer !== $nextAttributeTargetPointer) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'Multiple attributes per line are disallowed.',
			$nextAttributeOpenerPointer,
			self::CODE_DISALLOWED_MULTIPLE_ATTRIBUTES_PER_LINE,
		);

		if (!$fix) {
			return;
		}

		$nonWhitespacePointerBefore = TokenHelper::findPreviousNonWhitespace($phpcsFile, $nextAttributeOpenerPointer - 1);
		$indentation = IndentationHelper::getIndentation(
			$phpcsFile,
			TokenHelper::findFirstNonWhitespaceOnLine($phpcsFile, $attributeOpenerPointer),
		);

		$phpcsFile->fixer->beginChangeset();

		FixerHelper::removeBetween($phpcsFile, $nonWhitespacePointerBefore, $nextAttributeOpenerPointer);

		$phpcsFile->fixer->addContentBefore($nextAttributeOpenerPointer, $phpcsFile->eolChar . $indentation);

		$phpcsFile->fixer->endChangeset();
	}

}
