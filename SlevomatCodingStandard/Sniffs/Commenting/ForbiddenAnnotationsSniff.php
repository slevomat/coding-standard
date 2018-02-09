<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;

class ForbiddenAnnotationsSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	public const CODE_ANNOTATION_FORBIDDEN = 'AnnotationForbidden';

	/** @var string[] */
	public $forbiddenAnnotations = [];

	/** @var int[]|null [string => int] */
	private $normalizedForbiddenAnnotations;

	/**
	 * @return mixed[]
	 */
	public function register(): array
	{
		return [
			T_DOC_COMMENT_TAG,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $annotationPointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $annotationPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$annotationName = $tokens[$annotationPointer]['content'];
		// Fix for wrong PHPCS parsing
		$annotationName = preg_replace('~^(@[a-zA-Z\\\\]+)(.+|$)~', '\\1', $annotationName);
		if (!in_array($annotationName, $this->getNormalizedForbiddenAnnotations(), true)) {
			return;
		}

		$fix = $phpcsFile->addFixableError(sprintf('Use of annotation %s is forbidden.', $annotationName), $annotationPointer, self::CODE_ANNOTATION_FORBIDDEN);
		if (!$fix) {
			return;
		}

		/** @var int $docCommentOpenPointer */
		$docCommentOpenPointer = TokenHelper::findPrevious($phpcsFile, T_DOC_COMMENT_OPEN_TAG, $annotationPointer - 1);
		$docCommentClosePointer = $tokens[$docCommentOpenPointer]['comment_closer'];

		/** @var int $annotationStartPointer */
		$annotationStartPointer = TokenHelper::findPrevious($phpcsFile, T_DOC_COMMENT_STAR, $annotationPointer - 1);

		/** @var int $nextPointer */
		$nextPointer = TokenHelper::findNext($phpcsFile, [T_DOC_COMMENT_TAG, T_DOC_COMMENT_CLOSE_TAG], $annotationPointer + 1);
		if ($tokens[$nextPointer]['code'] === T_DOC_COMMENT_TAG) {
			$nextPointer = TokenHelper::findPrevious($phpcsFile, T_DOC_COMMENT_STAR, $nextPointer - 1);
		}
		$annotationEndPointer = $nextPointer - 1;

		if ($tokens[$nextPointer]['code'] === T_DOC_COMMENT_CLOSE_TAG) {
			$pointerBeforeWhitespace = TokenHelper::findPreviousExcluding($phpcsFile, [T_DOC_COMMENT_WHITESPACE, T_DOC_COMMENT_STAR], $annotationStartPointer - 1);
			/** @var int $annotationStartPointer */
			$annotationStartPointer = TokenHelper::findNext($phpcsFile, T_DOC_COMMENT_STAR, $pointerBeforeWhitespace + 1);
		}

		$phpcsFile->fixer->beginChangeset();

		for ($i = $annotationStartPointer; $i <= $annotationEndPointer; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}

		$docCommentUseful = false;
		for ($i = $docCommentOpenPointer + 1; $i < $docCommentClosePointer; $i++) {
			$tokenContent = trim($phpcsFile->fixer->getTokenContent($i));
			if ($tokenContent === '' || $tokenContent === '*') {
				continue;
			}

			$docCommentUseful = true;
			break;
		}

		if (!$docCommentUseful) {
			/** @var int $nextPointerAfterDocComment */
			$nextPointerAfterDocComment = TokenHelper::findNextEffective($phpcsFile, $docCommentClosePointer + 1);
			for ($i = $docCommentOpenPointer; $i < $nextPointerAfterDocComment; $i++) {
				$phpcsFile->fixer->replaceToken($i, '');
			}
		}

		$phpcsFile->fixer->endChangeset();
	}

	/**
	 * @return int[] [string => int]
	 */
	private function getNormalizedForbiddenAnnotations(): array
	{
		if ($this->normalizedForbiddenAnnotations === null) {
			$this->normalizedForbiddenAnnotations = SniffSettingsHelper::normalizeArray($this->forbiddenAnnotations);
		}
		return $this->normalizedForbiddenAnnotations;
	}

}
