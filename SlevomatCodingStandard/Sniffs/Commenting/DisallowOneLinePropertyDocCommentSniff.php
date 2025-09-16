<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\DocCommentHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\PropertyHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function rtrim;
use function sprintf;
use const T_VARIABLE;
use const T_WHITESPACE;

class DisallowOneLinePropertyDocCommentSniff implements Sniff
{

	public const CODE_ONE_LINE_PROPERTY_COMMENT = 'OneLinePropertyComment';

	/**
	 * @return list<int>
	 */
	public function register(): array
	{
		return [T_VARIABLE];
	}

	public function process(File $phpcsFile, int $propertyPointer): void
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

		/** @var int $docCommentStartPointer */
		$docCommentStartPointer = DocCommentHelper::findDocCommentOpenPointer($phpcsFile, $propertyPointer);
		$docCommentEndPointer = $tokens[$docCommentStartPointer]['comment_closer'];
		$lineDifference = $tokens[$docCommentEndPointer]['line'] - $tokens[$docCommentStartPointer]['line'];

		// Already multi-line
		if ($lineDifference !== 0) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf(
				'Found one-line comment for property %s, use multi-line comment instead.',
				PropertyHelper::getFullyQualifiedName($phpcsFile, $propertyPointer),
			),
			$docCommentStartPointer,
			self::CODE_ONE_LINE_PROPERTY_COMMENT,
		);

		if (!$fix) {
			return;
		}

		$commentWhitespacePointer = TokenHelper::findPrevious($phpcsFile, [T_WHITESPACE], $docCommentStartPointer);
		$indent = ($commentWhitespacePointer !== null ? $tokens[$commentWhitespacePointer]['content'] : '') . ' ';

		$phpcsFile->fixer->beginChangeset();

		$phpcsFile->fixer->addNewline($docCommentStartPointer);
		FixerHelper::add($phpcsFile, $docCommentStartPointer, $indent);
		FixerHelper::add($phpcsFile, $docCommentStartPointer, '*');

		if ($docCommentEndPointer - 1 !== $docCommentStartPointer) {
			FixerHelper::replace(
				$phpcsFile,
				$docCommentEndPointer - 1,
				rtrim($phpcsFile->fixer->getTokenContent($docCommentEndPointer - 1), ' '),
			);
		}

		FixerHelper::addBefore($phpcsFile, $docCommentEndPointer, $indent);
		$phpcsFile->fixer->addNewlineBefore($docCommentEndPointer);

		$phpcsFile->fixer->endChangeset();
	}

}
