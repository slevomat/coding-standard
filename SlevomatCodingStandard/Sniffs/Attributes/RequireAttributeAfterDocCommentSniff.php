<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Attributes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\AttributeHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use const T_ATTRIBUTE;
use const T_ATTRIBUTE_END;
use const T_DOC_COMMENT_OPEN_TAG;
use const T_WHITESPACE;

class RequireAttributeAfterDocCommentSniff implements Sniff
{

	public const CODE_ATTRIBUTE_BEFORE_DOC_COMMENT = 'AttributeBeforeDocComment';

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

		$docCommentOpenerPointer = TokenHelper::findNextExcluding(
			$phpcsFile,
			T_WHITESPACE,
			$tokens[$attributeOpenerPointer]['attribute_closer'] + 1
		);

		if ($tokens[$docCommentOpenerPointer]['code'] !== T_DOC_COMMENT_OPEN_TAG) {
			return;
		}

		$docCommentStartPointer = TokenHelper::findFirstTokenOnLine($phpcsFile, $docCommentOpenerPointer);
		$docCommentEndPointer = TokenHelper::findLastTokenOnLine($phpcsFile, $tokens[$docCommentOpenerPointer]['comment_closer']);
		$docComment = TokenHelper::getContent($phpcsFile, $docCommentStartPointer, $docCommentEndPointer);

		$firstAttributeOpenerPointer = $attributeOpenerPointer;
		do {
			$nonWhitespacePointerBefore = TokenHelper::findPreviousNonWhitespace($phpcsFile, $firstAttributeOpenerPointer - 1);

			if ($tokens[$nonWhitespacePointerBefore]['code'] !== T_ATTRIBUTE_END) {
				break;
			}

			$firstAttributeOpenerPointer = $tokens[$nonWhitespacePointerBefore]['attribute_opener'];
		} while (true);

		$attributeStartPointer = TokenHelper::findFirstTokenOnLine($phpcsFile, $firstAttributeOpenerPointer);

		$fix = $phpcsFile->addFixableError(
			'Attribute should be placed before documentation comment.',
			$attributeOpenerPointer,
			self::CODE_ATTRIBUTE_BEFORE_DOC_COMMENT
		);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		$phpcsFile->fixer->addContentBefore($attributeStartPointer, $docComment);

		FixerHelper::removeBetweenIncluding($phpcsFile, $docCommentStartPointer, $docCommentEndPointer);

		$phpcsFile->fixer->endChangeset();
	}

}
