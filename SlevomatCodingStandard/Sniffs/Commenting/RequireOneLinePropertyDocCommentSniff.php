<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

use SlevomatCodingStandard\Helpers\DocCommentHelper;
use SlevomatCodingStandard\Helpers\PropertyHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;

class RequireOneLinePropertyDocCommentSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	public const CODE_MULTI_LINE_PROPERTY_COMMENT = 'MultiLinePropertyComment';

	/**
	 * @return int[]
	 */
	public function register(): array
	{
		return [T_VARIABLE];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $propertyPointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $propertyPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		// not a property
		if (!PropertyHelper::isProperty($phpcsFile, $propertyPointer)) {
			return;
		}

		// only validate properties with comment
		if (!DocCommentHelper::hasDocComment($phpcsFile, $propertyPointer)) {
			return;
		}

		// only validate properties without description
		if (DocCommentHelper::hasDocCommentDescription($phpcsFile, $propertyPointer)) {
			return;
		}

		/** @var int $docCommentStartPointer */
		$docCommentStartPointer = DocCommentHelper::findDocCommentOpenToken($phpcsFile, $propertyPointer);
		$docCommentEndPointer = $tokens[$docCommentStartPointer]['comment_closer'];
		$lineDifference = $tokens[$docCommentEndPointer]['line'] - $tokens[$docCommentStartPointer]['line'];

		// already one-line
		if ($lineDifference === 0) {
			return;
		}

		// ignore empty lines
		for ($currentLinePointer = TokenHelper::findFirstTokenOnNextLine($phpcsFile, $docCommentStartPointer);
			$currentLinePointer !== null && $currentLinePointer < $docCommentEndPointer;
			$currentLinePointer = TokenHelper::findFirstTokenOnNextLine($phpcsFile, $currentLinePointer)) {
			$startingPointer = TokenHelper::findNext($phpcsFile, [T_DOC_COMMENT_STAR, T_DOC_COMMENT_CLOSE_TAG], $currentLinePointer, $docCommentEndPointer);

			if ($startingPointer === null || $tokens[$startingPointer]['code'] === T_DOC_COMMENT_CLOSE_TAG) {
				break;
			}

			$nextEffectivePointer = TokenHelper::findNextExcluding($phpcsFile, [T_DOC_COMMENT_WHITESPACE], $startingPointer + 1, $docCommentEndPointer + 1);

			if ($tokens[$currentLinePointer]['line'] === $tokens[$nextEffectivePointer]['line']) {
				continue;
			}

			$lineDifference--;
		}

		// looks like a compound doc-comment
		if ($lineDifference > 2) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf(
				'Found multi-line comment for property %s with single line content, use one-line comment instead.',
				PropertyHelper::getFullyQualifiedName($phpcsFile, $propertyPointer)
			),
			$docCommentStartPointer,
			self::CODE_MULTI_LINE_PROPERTY_COMMENT
		);

		if (!$fix) {
			return;
		}

		$contentStartPointer = TokenHelper::findNextExcluding($phpcsFile, [T_DOC_COMMENT_WHITESPACE, T_DOC_COMMENT_STAR], $docCommentStartPointer + 1, $docCommentEndPointer);
		$contentEndPointer = TokenHelper::findPreviousExcluding($phpcsFile, [T_DOC_COMMENT_WHITESPACE, T_DOC_COMMENT_STAR], $docCommentEndPointer - 1, $docCommentStartPointer);

		if ($contentStartPointer === null) {
			for ($i = $docCommentStartPointer + 1; $i < $docCommentEndPointer; $i++) {
				$phpcsFile->fixer->replaceToken($i, '');
			}

			return;
		}

		$phpcsFile->fixer->beginChangeset();

		for ($i = $docCommentStartPointer + 1; $i < $docCommentEndPointer; $i++) {
			if ($i >= $contentStartPointer && $i <= $contentEndPointer) {
				if ($i === $contentEndPointer) {
					$phpcsFile->fixer->replaceToken($i, rtrim($phpcsFile->fixer->getTokenContent($i), ' '));
				}

				continue;
			}

			$phpcsFile->fixer->replaceToken($i, '');
		}

		$phpcsFile->fixer->addContentBefore($contentStartPointer, ' ');
		$phpcsFile->fixer->addContentBefore($docCommentEndPointer, ' ');

		$phpcsFile->fixer->endChangeset();
	}

}
