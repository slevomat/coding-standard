<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

use SlevomatCodingStandard\Helpers\Annotation;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\DocCommentHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;

class DocCommentSpacingSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	public const CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTENT = 'IncorrectLinesCountBeforeFirstContent';
	public const CODE_INCORRECT_LINES_COUNT_BETWEEN_DESCRIPTION_AND_ANNOTATIONS = 'IncorrectLinesCountBetweenDescriptionAndAnnotations';
	public const CODE_INCORRECT_LINES_COUNT_BETWEEN_DIFFERENT_ANNOTATIONS_TYPES = 'IncorrectLinesCountBetweenDifferentAnnotationsTypes';
	public const CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTENT = 'IncorrectLinesCountAfterLastContent';

	/** @var int */
	public $linesCountBeforeFirstContent = 0;

	/** @var int */
	public $linesCountBetweenDescriptionAndAnnotations = 1;

	/** @var int */
	public $linesCountBetweenDifferentAnnotationsTypes = 0;

	/** @var int */
	public $linesCountAfterLastContent = 0;

	/**
	 * @return mixed[]
	 */
	public function register(): array
	{
		return [
			T_DOC_COMMENT_OPEN_TAG,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $docCommentOpenPointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $docCommentOpenPointer): void
	{
		if (DocCommentHelper::isInline($phpcsFile, $docCommentOpenPointer)) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		$firstContentStartPointer = TokenHelper::findNextExcluding(
			$phpcsFile,
			[T_DOC_COMMENT_WHITESPACE, T_DOC_COMMENT_STAR],
			$docCommentOpenPointer + 1,
			$tokens[$docCommentOpenPointer]['comment_closer']
		);

		if ($firstContentStartPointer === null) {
			return;
		}

		$firstContentEndPointer = $firstContentStartPointer;
		$actualPointer = $firstContentStartPointer;
		do {
			/** @var int $actualPointer */
			$actualPointer = TokenHelper::findNextExcluding(
				$phpcsFile,
				[T_DOC_COMMENT_STAR, T_DOC_COMMENT_WHITESPACE],
				$actualPointer + 1,
				$tokens[$docCommentOpenPointer]['comment_closer'] + 1
			);

			if ($tokens[$actualPointer]['code'] !== T_DOC_COMMENT_STRING) {
				break;
			}

			$firstContentEndPointer = $actualPointer;
		} while (true);

		$annotations = array_merge([], ...array_values(AnnotationHelper::getAnnotations($phpcsFile, $docCommentOpenPointer)));
		uasort($annotations, function (Annotation $a, Annotation $b): int {
			return $a->getStartPointer() <=> $b->getEndPointer();
		});
		$annotationsCount = count($annotations);

		$firstAnnotation = $annotationsCount > 0 ? $annotations[0] : null;

		$lastContentEndPointer = $annotationsCount > 0 ? $annotations[$annotationsCount - 1]->getEndPointer() : $firstContentEndPointer;

		$this->checkLinesBeforeFirstContent($phpcsFile, $docCommentOpenPointer, $firstContentStartPointer);
		$this->checkLinesBetweenDescriptionAndFirstAnnotation($phpcsFile, $docCommentOpenPointer, $firstContentStartPointer, $firstContentEndPointer, $firstAnnotation);
		$this->checkLinesBetweenDifferentAnnotationsTypes($phpcsFile, $docCommentOpenPointer, $annotations);
		$this->checkLinesAfterLastContent($phpcsFile, $docCommentOpenPointer, $tokens[$docCommentOpenPointer]['comment_closer'], $lastContentEndPointer);
	}

	private function checkLinesBeforeFirstContent(
		\PHP_CodeSniffer\Files\File $phpcsFile,
		int $docCommentOpenPointer,
		int $firstContentStartPointer
	): void
	{
		$tokens = $phpcsFile->getTokens();

		$whitespaceBeforeFirstContent = substr($tokens[$docCommentOpenPointer]['content'], 0, strlen('/**'));
		$whitespaceBeforeFirstContent .= TokenHelper::getContent($phpcsFile, $docCommentOpenPointer + 1, $firstContentStartPointer - 1);

		$requiredLinesCountBeforeFirstContent = SniffSettingsHelper::normalizeInteger($this->linesCountBeforeFirstContent);
		$linesCountBeforeFirstContent = max(substr_count($whitespaceBeforeFirstContent, $phpcsFile->eolChar) - 1, 0);
		if ($linesCountBeforeFirstContent === $requiredLinesCountBeforeFirstContent) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf('Expected %d lines before first content, found %d.', $requiredLinesCountBeforeFirstContent, $linesCountBeforeFirstContent),
			$firstContentStartPointer,
			self::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTENT
		);

		if (!$fix) {
			return;
		}

		$indentation = $this->getIndentation($phpcsFile, $docCommentOpenPointer);

		$phpcsFile->fixer->beginChangeset();

		$phpcsFile->fixer->replaceToken($docCommentOpenPointer, '/**' . $phpcsFile->eolChar);
		for ($i = $docCommentOpenPointer + 1; $i < $firstContentStartPointer; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}

		for ($i = 1; $i <= $requiredLinesCountBeforeFirstContent; $i++) {
			$phpcsFile->fixer->addContent($docCommentOpenPointer, sprintf('%s *%s', $indentation, $phpcsFile->eolChar));
		}

		$phpcsFile->fixer->addContentBefore($firstContentStartPointer, $this->getIndentation($phpcsFile, $firstContentStartPointer));

		$phpcsFile->fixer->endChangeset();
	}

	private function checkLinesBetweenDescriptionAndFirstAnnotation(
		\PHP_CodeSniffer\Files\File $phpcsFile,
		int $docCommentOpenPointer,
		int $firstContentStartPointer,
		int $firstContentEndPointer,
		?Annotation $firstAnnotation
	): void
	{
		if ($firstAnnotation === null) {
			return;
		}

		if ($firstContentStartPointer === $firstAnnotation->getStartPointer()) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		preg_match('~(\\s+)$~', $tokens[$firstContentEndPointer]['content'], $matches);

		$whitespaceBetweenDescriptionAndFirstAnnotation = $matches[1] ?? '';
		$whitespaceBetweenDescriptionAndFirstAnnotation .= TokenHelper::getContent($phpcsFile, $firstContentEndPointer + 1, $firstAnnotation->getStartPointer() - 1);

		$requiredLinesCountBetweenDescriptionAndAnnotations = SniffSettingsHelper::normalizeInteger($this->linesCountBetweenDescriptionAndAnnotations);
		$linesCountBetweenDescriptionAndAnnotations = max(substr_count($whitespaceBetweenDescriptionAndFirstAnnotation, $phpcsFile->eolChar) - 1, 0);
		if ($linesCountBetweenDescriptionAndAnnotations === $requiredLinesCountBetweenDescriptionAndAnnotations) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf('Expected %d lines between description and annotations, found %d.', $requiredLinesCountBetweenDescriptionAndAnnotations, $linesCountBetweenDescriptionAndAnnotations),
			$firstAnnotation->getStartPointer(),
			self::CODE_INCORRECT_LINES_COUNT_BETWEEN_DESCRIPTION_AND_ANNOTATIONS
		);

		if (!$fix) {
			return;
		}

		$indentation = $this->getIndentation($phpcsFile, $docCommentOpenPointer);

		$phpcsFile->fixer->beginChangeset();

		$phpcsFile->fixer->addNewline($firstContentEndPointer);
		for ($i = $firstContentEndPointer + 1; $i < $firstAnnotation->getStartPointer(); $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}

		for ($i = 1; $i <= $requiredLinesCountBetweenDescriptionAndAnnotations; $i++) {
			$phpcsFile->fixer->addContent($firstContentEndPointer, sprintf('%s *%s', $indentation, $phpcsFile->eolChar));
		}

		$phpcsFile->fixer->addContentBefore($firstAnnotation->getStartPointer(), $this->getIndentation($phpcsFile, $firstAnnotation->getStartPointer()));

		$phpcsFile->fixer->endChangeset();
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $docCommentOpenPointer
	 * @param \SlevomatCodingStandard\Helpers\Annotation[] $annotations
	 */
	private function checkLinesBetweenDifferentAnnotationsTypes(
		\PHP_CodeSniffer\Files\File $phpcsFile,
		int $docCommentOpenPointer,
		array $annotations
	): void
	{
		if (count($annotations) <= 1) {
			return;
		}

		$requiredLinesCountBetweenDifferentAnnotationsTypes = SniffSettingsHelper::normalizeInteger($this->linesCountBetweenDifferentAnnotationsTypes);

		$tokens = $phpcsFile->getTokens();

		$indentation = $this->getIndentation($phpcsFile, $docCommentOpenPointer);

		$previousAnnotation = null;
		foreach ($annotations as $no => $annotation) {
			if ($previousAnnotation === null) {
				$previousAnnotation = $annotation;
				continue;
			}

			if ($annotation->getName() === $previousAnnotation->getName()) {
				$previousAnnotation = $annotation;
				continue;
			}

			preg_match('~(\\s+)$~', $tokens[$previousAnnotation->getEndPointer()]['content'], $matches);

			$linesCountAfterPreviousAnnotation = $matches[1] ?? '';
			$linesCountAfterPreviousAnnotation .= TokenHelper::getContent($phpcsFile, $previousAnnotation->getEndPointer() + 1, $annotation->getStartPointer() - 1);

			$linesCountAfterPreviousAnnotation = max(substr_count($linesCountAfterPreviousAnnotation, $phpcsFile->eolChar) - 1, 0);

			if ($linesCountAfterPreviousAnnotation === $requiredLinesCountBetweenDifferentAnnotationsTypes) {
				$previousAnnotation = $annotation;
				continue;
			}

			$fix = $phpcsFile->addFixableError(
				sprintf(
					'Expected %d lines between different annotations types, found %d.',
					$requiredLinesCountBetweenDifferentAnnotationsTypes,
					$linesCountAfterPreviousAnnotation
				),
				$annotation->getStartPointer(),
				self::CODE_INCORRECT_LINES_COUNT_BETWEEN_DIFFERENT_ANNOTATIONS_TYPES
			);

			if (!$fix) {
				$previousAnnotation = $annotation;
				continue;
			}

			$phpcsFile->fixer->beginChangeset();

			$phpcsFile->fixer->addNewline($previousAnnotation->getEndPointer());
			for ($i = $previousAnnotation->getEndPointer() + 1; $i < $annotation->getStartPointer(); $i++) {
				$phpcsFile->fixer->replaceToken($i, '');
			}

			for ($i = 1; $i <= $requiredLinesCountBetweenDifferentAnnotationsTypes; $i++) {
				$phpcsFile->fixer->addContent($previousAnnotation->getEndPointer(), sprintf('%s *%s', $indentation, $phpcsFile->eolChar));
			}

			$phpcsFile->fixer->addContentBefore($annotation->getStartPointer(), $this->getIndentation($phpcsFile, $annotation->getStartPointer()));

			$phpcsFile->fixer->endChangeset();
		}
	}

	private function checkLinesAfterLastContent(
		\PHP_CodeSniffer\Files\File $phpcsFile,
		int $docCommentOpenPointer,
		int $docCommentClosePointer,
		int $lastContentEndPointer
	): void
	{
		$whitespaceAfterLastContent = TokenHelper::getContent($phpcsFile, $lastContentEndPointer + 1, $docCommentClosePointer);

		$requiredLinesCountAfterLastContent = SniffSettingsHelper::normalizeInteger($this->linesCountAfterLastContent);
		$linesCountAfterLastContent = max(substr_count($whitespaceAfterLastContent, $phpcsFile->eolChar) - 1, 0);
		if ($linesCountAfterLastContent === $requiredLinesCountAfterLastContent) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf('Expected %d lines after last content, found %d.', $requiredLinesCountAfterLastContent, $linesCountAfterLastContent),
			$lastContentEndPointer,
			self::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTENT
		);

		if (!$fix) {
			return;
		}

		$indentation = $this->getIndentation($phpcsFile, $docCommentOpenPointer);

		$phpcsFile->fixer->beginChangeset();

		$phpcsFile->fixer->addNewline($lastContentEndPointer);
		for ($i = $lastContentEndPointer + 1; $i < $docCommentClosePointer; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}

		for ($i = 1; $i <= $requiredLinesCountAfterLastContent; $i++) {
			$phpcsFile->fixer->addContent($lastContentEndPointer, sprintf('%s *%s', $indentation, $phpcsFile->eolChar));
		}

		$phpcsFile->fixer->addContentBefore($docCommentClosePointer, $this->getIndentation($phpcsFile, $docCommentClosePointer));

		$phpcsFile->fixer->endChangeset();
	}

	private function getIndentation(\PHP_CodeSniffer\Files\File $phpcsFile, int $pointer): string
	{
		$pointerBeforeDocComment = TokenHelper::findPreviousExcluding($phpcsFile, [T_WHITESPACE, T_DOC_COMMENT_WHITESPACE, T_DOC_COMMENT_STAR], $pointer - 1);
		$whitespaceBeforeDocComment = TokenHelper::getContent($phpcsFile, $pointerBeforeDocComment + 1, $pointer - 1);

		preg_match('~([^' . $phpcsFile->eolChar . ']+)$~', $whitespaceBeforeDocComment, $matches);

		return $matches[1] ?? '';
	}

}
