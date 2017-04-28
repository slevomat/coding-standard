<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;

class ForbiddenAnnotationsSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	const CODE_ANNOTATION_FORBIDDEN = 'AnnotationForbidden';

	/** @var string[] */
	public $forbiddenAnnotations = [];

	/** @var int[] [string => int] */
	private $normalizedForbiddenAnnotations;

	/**
	 * @return int[]
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
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $annotationPointer)
	{
		$tokens = $phpcsFile->getTokens();

		$annotationName = $tokens[$annotationPointer]['content'];
		// Fix for wrong PHPCS parsing
		$annotationName = preg_replace('~^(@[a-zA-Z\\\\]+)(.+|$)~', '\\1', $annotationName);
		if (!in_array($annotationName, $this->getNormalizedForbiddenAnnotations(), true)) {
			return;
		}

		$fix = $phpcsFile->addFixableError(sprintf('Use of annotation %s is forbidden.', $annotationName), $annotationPointer, self::CODE_ANNOTATION_FORBIDDEN);
		if ($fix) {
			$docCommentOpenPointer = $phpcsFile->findPrevious(T_DOC_COMMENT_OPEN_TAG, $annotationPointer - 1);
			$docCommentClosePointer = $tokens[$docCommentOpenPointer]['comment_closer'];

			$annotationStartPointer = $phpcsFile->findPrevious(T_DOC_COMMENT_STAR, $annotationPointer - 1);
			$nextPointer = $phpcsFile->findNext([T_DOC_COMMENT_TAG, T_DOC_COMMENT_CLOSE_TAG], $annotationPointer + 1);
			if ($tokens[$nextPointer]['code'] === T_DOC_COMMENT_TAG) {
				$nextPointer = $phpcsFile->findPrevious(T_DOC_COMMENT_STAR, $nextPointer - 1);
			}
			$annotationEndPointer = $nextPointer - 1;

			$phpcsFile->fixer->beginChangeset();
			for ($i = $annotationStartPointer; $i <= $annotationEndPointer; $i++) {
				$phpcsFile->fixer->replaceToken($i, '');
			}

			$docCommentUseful = false;
			for ($i = $docCommentOpenPointer + 1; $i < $docCommentClosePointer; $i++) {
				$tokenContent = trim($phpcsFile->fixer->getTokenContent($i));
				if ($tokenContent !== '' && $tokenContent !== '*') {
					$docCommentUseful = true;
					break;
				}
			}

			if (!$docCommentUseful) {
				for ($i = $docCommentOpenPointer; $i < TokenHelper::findNextEffective($phpcsFile, $docCommentClosePointer + 1); $i++) {
					$phpcsFile->fixer->replaceToken($i, '');
				}
			}

			$phpcsFile->fixer->endChangeset();
		}
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
