<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\DocCommentHelper;
use SlevomatCodingStandard\Helpers\PropertyHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function rtrim;
use function sprintf;
use const T_DOC_COMMENT_CLOSE_TAG;
use const T_DOC_COMMENT_STAR;
use const T_DOC_COMMENT_WHITESPACE;
use const T_VARIABLE;

class RequireOneLinePropertyDocCommentSniff implements Sniff
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
	public function process(File $phpcsFile, $propertyPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		// Not a property
		if (!PropertyHelper::isProperty($phpcsFile, $propertyPointer)) {
			return;
		}

		// Only validate properties with comment
		if (!DocCommentHelper::hasDocComment($phpcsFile, $propertyPointer)) {
			return;
		}

		// Only validate properties without description
		if (DocCommentHelper::hasDocCommentDescription($phpcsFile, $propertyPointer)) {
			return;
		}

		/** @var int $docCommentStartPointer */
		$docCommentStartPointer = DocCommentHelper::findDocCommentOpenToken($phpcsFile, $propertyPointer);
		$docCommentEndPointer = $tokens[$docCommentStartPointer]['comment_closer'];
		$lineDifference = $tokens[$docCommentEndPointer]['line'] - $tokens[$docCommentStartPointer]['line'];

		// Already one-line
		if ($lineDifference === 0) {
			return;
		}

		// Ignore empty lines
		$currentLinePointer = $docCommentStartPointer;
		do {
			$currentLinePointer = TokenHelper::findFirstTokenOnNextLine($phpcsFile, $currentLinePointer);

			if ($currentLinePointer === null || $currentLinePointer >= $docCommentEndPointer) {
				break;
			}

			$startingPointer = TokenHelper::findNext($phpcsFile, [T_DOC_COMMENT_STAR, T_DOC_COMMENT_CLOSE_TAG], $currentLinePointer, $docCommentEndPointer);

			if ($startingPointer === null || $tokens[$startingPointer]['code'] === T_DOC_COMMENT_CLOSE_TAG) {
				break;
			}

			$nextEffectivePointer = TokenHelper::findNextExcluding($phpcsFile, [T_DOC_COMMENT_WHITESPACE], $startingPointer + 1, $docCommentEndPointer + 1);

			if ($tokens[$currentLinePointer]['line'] === $tokens[$nextEffectivePointer]['line']) {
				continue;
			}

			$lineDifference--;
		} while (true);

		// Looks like a compound doc-comment
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
