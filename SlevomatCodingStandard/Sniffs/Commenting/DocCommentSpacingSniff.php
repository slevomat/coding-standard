<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\Annotation;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\DocCommentHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\IndentationHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function array_combine;
use function array_diff;
use function array_flip;
use function array_key_exists;
use function array_keys;
use function array_map;
use function array_values;
use function asort;
use function count;
use function explode;
use function in_array;
use function ksort;
use function max;
use function preg_match;
use function sprintf;
use function strlen;
use function strpos;
use function substr;
use function substr_count;
use function trim;
use function usort;
use const T_DOC_COMMENT_OPEN_TAG;
use const T_DOC_COMMENT_STAR;
use const T_DOC_COMMENT_WHITESPACE;

class DocCommentSpacingSniff implements Sniff
{

	public const CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTENT = 'IncorrectLinesCountBeforeFirstContent';
	public const CODE_INCORRECT_LINES_COUNT_BETWEEN_DESCRIPTION_AND_ANNOTATIONS = 'IncorrectLinesCountBetweenDescriptionAndAnnotations';
	public const CODE_INCORRECT_LINES_COUNT_BETWEEN_DIFFERENT_ANNOTATIONS_TYPES = 'IncorrectLinesCountBetweenDifferentAnnotationsTypes';
	public const CODE_INCORRECT_LINES_COUNT_BETWEEN_ANNOTATIONS_GROUPS = 'IncorrectLinesCountBetweenAnnotationsGroups';
	public const CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTENT = 'IncorrectLinesCountAfterLastContent';
	public const CODE_INCORRECT_ANNOTATIONS_GROUP = 'IncorrectAnnotationsGroup';
	public const CODE_INCORRECT_ORDER_OF_ANNOTATIONS_GROUPS = 'IncorrectOrderOfAnnotationsGroup';
	public const CODE_INCORRECT_ORDER_OF_ANNOTATIONS_IN_GROUP = 'IncorrectOrderOfAnnotationsInGroup';

	public int $linesCountBeforeFirstContent = 0;

	public int $linesCountBetweenDescriptionAndAnnotations = 1;

	public int $linesCountBetweenDifferentAnnotationsTypes = 0;

	public int $linesCountBetweenAnnotationsGroups = 1;

	public int $linesCountAfterLastContent = 0;

	/** @var list<string> */
	public array $annotationsGroups = [];

	/** @var array<list<string>>|null */
	private ?array $normalizedAnnotationsGroups = null;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_DOC_COMMENT_OPEN_TAG,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $docCommentOpenerPointer
	 */
	public function process(File $phpcsFile, $docCommentOpenerPointer): void
	{
		$this->linesCountBeforeFirstContent = SniffSettingsHelper::normalizeInteger($this->linesCountBeforeFirstContent);
		$this->linesCountBetweenDescriptionAndAnnotations = SniffSettingsHelper::normalizeInteger(
			$this->linesCountBetweenDescriptionAndAnnotations,
		);
		$this->linesCountBetweenDifferentAnnotationsTypes = SniffSettingsHelper::normalizeInteger(
			$this->linesCountBetweenDifferentAnnotationsTypes,
		);
		$this->linesCountBetweenAnnotationsGroups = SniffSettingsHelper::normalizeInteger($this->linesCountBetweenAnnotationsGroups);
		$this->linesCountAfterLastContent = SniffSettingsHelper::normalizeInteger($this->linesCountAfterLastContent);

		if (DocCommentHelper::isInline($phpcsFile, $docCommentOpenerPointer)) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		if (TokenHelper::findNextExcluding(
			$phpcsFile,
			[T_DOC_COMMENT_WHITESPACE, T_DOC_COMMENT_STAR],
			$docCommentOpenerPointer + 1,
			$tokens[$docCommentOpenerPointer]['comment_closer'],
		) === null) {
			return;
		}

		$parsedDocComment = DocCommentHelper::parseDocComment($phpcsFile, $docCommentOpenerPointer);

		if ($parsedDocComment === null) {
			return;
		}

		$firstContentStartPointer = $parsedDocComment->getNodeStartPointer($phpcsFile, $parsedDocComment->getNode()->children[0]);
		$firstContentEndPointer = $parsedDocComment->getNodeEndPointer(
			$phpcsFile,
			$parsedDocComment->getNode()->children[0],
			$firstContentStartPointer,
		);

		$annotations = AnnotationHelper::getAnnotations($phpcsFile, $docCommentOpenerPointer);
		usort($annotations, static fn (Annotation $a, Annotation $b): int => $a->getStartPointer() <=> $b->getStartPointer());
		$annotationsCount = count($annotations);

		$firstAnnotationPointer = $annotationsCount > 0 ? $annotations[0]->getStartPointer() : null;

		/** @var int $lastContentEndPointer */
		$lastContentEndPointer = $annotationsCount > 0
			? $annotations[$annotationsCount - 1]->getEndPointer()
			: $firstContentEndPointer;

		$this->checkLinesBeforeFirstContent($phpcsFile, $docCommentOpenerPointer, $firstContentStartPointer);
		$this->checkLinesBetweenDescriptionAndFirstAnnotation(
			$phpcsFile,
			$docCommentOpenerPointer,
			$firstContentStartPointer,
			$firstContentEndPointer,
			$firstAnnotationPointer,
		);

		if (count($annotations) > 1) {
			if (count($this->getAnnotationsGroups()) === 0) {
				$this->checkLinesBetweenDifferentAnnotationsTypes($phpcsFile, $docCommentOpenerPointer, $annotations);
			} else {
				$this->checkAnnotationsGroups($phpcsFile, $docCommentOpenerPointer, $annotations);
			}
		}

		$this->checkLinesAfterLastContent(
			$phpcsFile,
			$docCommentOpenerPointer,
			$tokens[$docCommentOpenerPointer]['comment_closer'],
			$lastContentEndPointer,
		);
	}

	private function checkLinesBeforeFirstContent(File $phpcsFile, int $docCommentOpenerPointer, int $firstContentStartPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$whitespaceBeforeFirstContent = substr($tokens[$docCommentOpenerPointer]['content'], 0, strlen('/**'));
		$whitespaceBeforeFirstContent .= TokenHelper::getContent($phpcsFile, $docCommentOpenerPointer + 1, $firstContentStartPointer - 1);

		$linesCountBeforeFirstContent = max(substr_count($whitespaceBeforeFirstContent, $phpcsFile->eolChar) - 1, 0);
		if ($linesCountBeforeFirstContent === $this->linesCountBeforeFirstContent) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf(
				'Expected %d line%s before first content, found %d.',
				$this->linesCountBeforeFirstContent,
				$this->linesCountBeforeFirstContent === 1 ? '' : 's',
				$linesCountBeforeFirstContent,
			),
			$firstContentStartPointer,
			self::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTENT,
		);

		if (!$fix) {
			return;
		}

		$indentation = IndentationHelper::getIndentation($phpcsFile, $docCommentOpenerPointer);

		$phpcsFile->fixer->beginChangeset();

		FixerHelper::change($phpcsFile, $docCommentOpenerPointer, $firstContentStartPointer - 1, '/**' . $phpcsFile->eolChar);

		for ($i = 1; $i <= $this->linesCountBeforeFirstContent; $i++) {
			$phpcsFile->fixer->addContent($docCommentOpenerPointer, sprintf('%s *%s', $indentation, $phpcsFile->eolChar));
		}

		$phpcsFile->fixer->addContentBefore($firstContentStartPointer, $indentation . ' * ');

		$phpcsFile->fixer->endChangeset();
	}

	private function checkLinesBetweenDescriptionAndFirstAnnotation(
		File $phpcsFile,
		int $docCommentOpenerPointer,
		int $firstContentStartPointer,
		int $firstContentEndPointer,
		?int $firstAnnotationPointer
	): void
	{
		if ($firstAnnotationPointer === null) {
			return;
		}

		if ($firstContentStartPointer === $firstAnnotationPointer) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		preg_match('~(\\s+)$~', $tokens[$firstContentEndPointer]['content'], $matches);

		$whitespaceBetweenDescriptionAndFirstAnnotation = $matches[1] ?? '';
		$whitespaceBetweenDescriptionAndFirstAnnotation .= TokenHelper::getContent(
			$phpcsFile,
			$firstContentEndPointer + 1,
			$firstAnnotationPointer - 1,
		);

		$linesCountBetweenDescriptionAndAnnotations = max(
			substr_count($whitespaceBetweenDescriptionAndFirstAnnotation, $phpcsFile->eolChar) - 1,
			0,
		);
		if ($linesCountBetweenDescriptionAndAnnotations === $this->linesCountBetweenDescriptionAndAnnotations) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf(
				'Expected %d line%s between description and annotations, found %d.',
				$this->linesCountBetweenDescriptionAndAnnotations,
				$this->linesCountBetweenDescriptionAndAnnotations === 1 ? '' : 's',
				$linesCountBetweenDescriptionAndAnnotations,
			),
			$firstAnnotationPointer,
			self::CODE_INCORRECT_LINES_COUNT_BETWEEN_DESCRIPTION_AND_ANNOTATIONS,
		);

		if (!$fix) {
			return;
		}

		$indentation = IndentationHelper::getIndentation($phpcsFile, $docCommentOpenerPointer);

		$phpcsFile->fixer->beginChangeset();

		$phpcsFile->fixer->addNewline($firstContentEndPointer);

		FixerHelper::removeBetween($phpcsFile, $firstContentEndPointer, $firstAnnotationPointer);

		for ($i = 1; $i <= $this->linesCountBetweenDescriptionAndAnnotations; $i++) {
			$phpcsFile->fixer->addContent($firstContentEndPointer, sprintf('%s *%s', $indentation, $phpcsFile->eolChar));
		}

		$phpcsFile->fixer->addContentBefore($firstAnnotationPointer, $indentation . ' * ');

		$phpcsFile->fixer->endChangeset();
	}

	/**
	 * @param list<Annotation> $annotations
	 */
	private function checkLinesBetweenDifferentAnnotationsTypes(File $phpcsFile, int $docCommentOpenerPointer, array $annotations): void
	{
		$indentation = IndentationHelper::getIndentation($phpcsFile, $docCommentOpenerPointer);

		$previousAnnotation = null;
		foreach ($annotations as $annotation) {
			if ($previousAnnotation === null) {
				$previousAnnotation = $annotation;
				continue;
			}

			if ($annotation->getName() === $previousAnnotation->getName()) {
				$previousAnnotation = $annotation;
				continue;
			}

			$whitespaceAfterPreviousAnnotation = TokenHelper::getContent(
				$phpcsFile,
				$previousAnnotation->getEndPointer() + 1,
				$annotation->getStartPointer() - 1,
			);

			$linesCountAfterPreviousAnnotation = max(substr_count($whitespaceAfterPreviousAnnotation, $phpcsFile->eolChar) - 1, 0);

			if ($linesCountAfterPreviousAnnotation === $this->linesCountBetweenDifferentAnnotationsTypes) {
				$previousAnnotation = $annotation;
				continue;
			}

			$fix = $phpcsFile->addFixableError(
				sprintf(
					'Expected %d line%s between different annotations types, found %d.',
					$this->linesCountBetweenDifferentAnnotationsTypes,
					$this->linesCountBetweenDifferentAnnotationsTypes === 1 ? '' : 's',
					$linesCountAfterPreviousAnnotation,
				),
				$annotation->getStartPointer(),
				self::CODE_INCORRECT_LINES_COUNT_BETWEEN_DIFFERENT_ANNOTATIONS_TYPES,
			);

			if (!$fix) {
				$previousAnnotation = $annotation;
				continue;
			}

			$phpcsFile->fixer->beginChangeset();

			FixerHelper::removeBetween($phpcsFile, $previousAnnotation->getEndPointer(), $annotation->getStartPointer());

			$phpcsFile->fixer->addNewline($previousAnnotation->getEndPointer());

			for ($i = 1; $i <= $this->linesCountBetweenDifferentAnnotationsTypes; $i++) {
				$phpcsFile->fixer->addContent($previousAnnotation->getEndPointer(), sprintf('%s *%s', $indentation, $phpcsFile->eolChar));
			}

			$phpcsFile->fixer->addContentBefore($annotation->getStartPointer(), $indentation . ' * ');

			$phpcsFile->fixer->endChangeset();

			$previousAnnotation = $annotation;
		}
	}

	/**
	 * @param list<Annotation> $annotations
	 */
	private function checkAnnotationsGroups(File $phpcsFile, int $docCommentOpenerPointer, array $annotations): void
	{
		$tokens = $phpcsFile->getTokens();

		$annotationsGroups = [];
		$annotationsGroup = [];
		$previousAnnotation = null;
		foreach ($annotations as $annotation) {
			if (
				$previousAnnotation === null
				|| $tokens[$previousAnnotation->getEndPointer()]['line'] + 1 === $tokens[$annotation->getStartPointer()]['line']
			) {
				$annotationsGroup[] = $annotation;
				$previousAnnotation = $annotation;
				continue;
			}

			$annotationsGroups[] = $annotationsGroup;
			$annotationsGroup = [$annotation];
			$previousAnnotation = $annotation;
		}

		if (count($annotationsGroup) > 0) {
			$annotationsGroups[] = $annotationsGroup;
		}

		$this->checkAnnotationsGroupsOrder($phpcsFile, $docCommentOpenerPointer, $annotationsGroups, $annotations);
		$this->checkLinesBetweenAnnotationsGroups($phpcsFile, $docCommentOpenerPointer, $annotationsGroups);
	}

	/**
	 * @param list<list<Annotation>> $annotationsGroups
	 */
	private function checkLinesBetweenAnnotationsGroups(File $phpcsFile, int $docCommentOpenerPointer, array $annotationsGroups): void
	{
		$tokens = $phpcsFile->getTokens();

		$previousAnnotationsGroup = null;
		foreach ($annotationsGroups as $annotationsGroup) {
			if ($previousAnnotationsGroup === null) {
				$previousAnnotationsGroup = $annotationsGroup;
				continue;
			}

			$lastAnnotationInPreviousGroup = $previousAnnotationsGroup[count($previousAnnotationsGroup) - 1];
			$firstAnnotationInActualGroup = $annotationsGroup[0];

			$actualLinesCountBetweenAnnotationsGroups = $tokens[$firstAnnotationInActualGroup->getStartPointer()]['line'] - $tokens[$lastAnnotationInPreviousGroup->getEndPointer()]['line'] - 1;
			if ($actualLinesCountBetweenAnnotationsGroups === $this->linesCountBetweenAnnotationsGroups) {
				$previousAnnotationsGroup = $annotationsGroup;
				continue;
			}

			$fix = $phpcsFile->addFixableError(
				sprintf(
					'Expected %d line%s between annotations groups, found %d.',
					$this->linesCountBetweenAnnotationsGroups,
					$this->linesCountBetweenAnnotationsGroups === 1 ? '' : 's',
					$actualLinesCountBetweenAnnotationsGroups,
				),
				$firstAnnotationInActualGroup->getStartPointer(),
				self::CODE_INCORRECT_LINES_COUNT_BETWEEN_ANNOTATIONS_GROUPS,
			);

			if (!$fix) {
				$previousAnnotationsGroup = $annotationsGroup;
				continue;
			}

			$indentation = IndentationHelper::getIndentation($phpcsFile, $docCommentOpenerPointer);

			$phpcsFile->fixer->beginChangeset();

			$phpcsFile->fixer->addNewline($lastAnnotationInPreviousGroup->getEndPointer());

			FixerHelper::removeBetween(
				$phpcsFile,
				$lastAnnotationInPreviousGroup->getEndPointer(),
				$firstAnnotationInActualGroup->getStartPointer(),
			);

			for ($i = 1; $i <= $this->linesCountBetweenAnnotationsGroups; $i++) {
				$phpcsFile->fixer->addContent(
					$lastAnnotationInPreviousGroup->getEndPointer(),
					sprintf('%s *%s', $indentation, $phpcsFile->eolChar),
				);
			}

			$phpcsFile->fixer->addContentBefore(
				$firstAnnotationInActualGroup->getStartPointer(),
				$indentation . ' * ',
			);

			$phpcsFile->fixer->endChangeset();
		}
	}

	/**
	 * @param list<list<Annotation>> $annotationsGroups
	 * @param list<Annotation> $annotations
	 */
	private function checkAnnotationsGroupsOrder(
		File $phpcsFile,
		int $docCommentOpenerPointer,
		array $annotationsGroups,
		array $annotations
	): void
	{
		$getAnnotationsPointers = static fn (Annotation $annotation): int => $annotation->getStartPointer();

		$equals = static function (array $firstAnnotationsGroup, array $secondAnnotationsGroup) use ($getAnnotationsPointers): bool {
			$firstAnnotationsPointers = array_map($getAnnotationsPointers, $firstAnnotationsGroup);
			$secondAnnotationsPointers = array_map($getAnnotationsPointers, $secondAnnotationsGroup);

			return count(array_diff($firstAnnotationsPointers, $secondAnnotationsPointers)) === 0
				&& count(array_diff($secondAnnotationsPointers, $firstAnnotationsPointers)) === 0;
		};

		$sortedAnnotationsGroups = $this->sortAnnotationsToGroups($annotations);
		$incorrectAnnotationsGroupsExist = false;
		$annotationsGroupsPositions = [];

		$fix = false;
		$undefinedAnnotationsGroups = [];
		foreach ($annotationsGroups as $annotationsGroupPosition => $annotationsGroup) {
			foreach ($sortedAnnotationsGroups as $sortedAnnotationsGroupPosition => $sortedAnnotationsGroup) {
				if ($equals($annotationsGroup, $sortedAnnotationsGroup)) {
					$annotationsGroupsPositions[$annotationsGroupPosition] = $sortedAnnotationsGroupPosition;
					continue 2;
				}

				$undefinedAnnotationsGroup = true;
				foreach ($annotationsGroup as $annotation) {
					foreach ($this->getAnnotationsGroups() as $annotationNames) {
						foreach ($annotationNames as $annotationName) {
							if ($this->isAnnotationMatched($annotation, $annotationName)) {
								$undefinedAnnotationsGroup = false;
								break 3;
							}
						}
					}
				}

				if ($undefinedAnnotationsGroup) {
					$undefinedAnnotationsGroups[] = $annotationsGroupPosition;
					continue 2;
				}
			}

			$incorrectAnnotationsGroupsExist = true;

			$fix = $phpcsFile->addFixableError(
				'Incorrect annotations group.',
				$annotationsGroup[0]->getStartPointer(),
				self::CODE_INCORRECT_ANNOTATIONS_GROUP,
			);
		}

		if (count($annotationsGroupsPositions) === 0 && count($undefinedAnnotationsGroups) > 1) {
			$incorrectAnnotationsGroupsExist = true;

			$fix = $phpcsFile->addFixableError(
				'Incorrect annotations group.',
				$annotationsGroups[0][0]->getStartPointer(),
				self::CODE_INCORRECT_ANNOTATIONS_GROUP,
			);
		}

		if (!$incorrectAnnotationsGroupsExist) {
			foreach ($undefinedAnnotationsGroups as $undefinedAnnotationsGroupPosition) {
				$annotationsGroupsPositions[$undefinedAnnotationsGroupPosition] = (count($annotationsGroupsPositions) > 0
					? max($annotationsGroupsPositions)
					: 0) + 1;
			}
			ksort($annotationsGroupsPositions);

			$positionsMappedToGroups = array_keys($annotationsGroupsPositions);
			$tmp = array_values($annotationsGroupsPositions);
			asort($tmp);
			$normalizedAnnotationsGroupsPositions = array_combine(array_keys($positionsMappedToGroups), array_keys($tmp));

			foreach ($normalizedAnnotationsGroupsPositions as $normalizedAnnotationsGroupPosition => $sortedAnnotationsGroupPosition) {
				if ($normalizedAnnotationsGroupPosition === $sortedAnnotationsGroupPosition) {
					continue;
				}

				$fix = $phpcsFile->addFixableError(
					'Incorrect order of annotations groups.',
					$annotationsGroups[$positionsMappedToGroups[$normalizedAnnotationsGroupPosition]][0]->getStartPointer(),
					self::CODE_INCORRECT_ORDER_OF_ANNOTATIONS_GROUPS,
				);
				break;
			}
		}

		foreach ($annotationsGroups as $annotationsGroupPosition => $annotationsGroup) {
			if (!array_key_exists($annotationsGroupPosition, $annotationsGroupsPositions)) {
				continue;
			}

			if (!array_key_exists($annotationsGroupsPositions[$annotationsGroupPosition], $sortedAnnotationsGroups)) {
				continue;
			}

			$sortedAnnotationsGroup = $sortedAnnotationsGroups[$annotationsGroupsPositions[$annotationsGroupPosition]];

			foreach ($annotationsGroup as $annotationPosition => $annotation) {
				if ($annotation === $sortedAnnotationsGroup[$annotationPosition]) {
					continue;
				}

				$fix = $phpcsFile->addFixableError(
					'Incorrect order of annotations in group.',
					$annotation->getStartPointer(),
					self::CODE_INCORRECT_ORDER_OF_ANNOTATIONS_IN_GROUP,
				);
				break;
			}
		}

		if (!$fix) {
			return;
		}

		$firstAnnotation = $annotationsGroups[0][0];
		$lastAnnotationsGroup = $annotationsGroups[count($annotationsGroups) - 1];
		$lastAnnotation = $lastAnnotationsGroup[count($lastAnnotationsGroup) - 1];

		$indentation = IndentationHelper::getIndentation($phpcsFile, $docCommentOpenerPointer);

		$fixedAnnotations = '';
		$firstGroup = true;
		foreach ($sortedAnnotationsGroups as $sortedAnnotationsGroup) {
			if ($firstGroup) {
				$firstGroup = false;
			} else {
				for ($i = 0; $i < $this->linesCountBetweenAnnotationsGroups; $i++) {
					$fixedAnnotations .= sprintf('%s *%s', $indentation, $phpcsFile->eolChar);
				}
			}

			foreach ($sortedAnnotationsGroup as $sortedAnnotation) {
				$fixedAnnotations .= sprintf(
					'%s * %s%s',
					$indentation,
					trim(TokenHelper::getContent($phpcsFile, $sortedAnnotation->getStartPointer(), $sortedAnnotation->getEndPointer())),
					$phpcsFile->eolChar,
				);
			}
		}

		$tokens = $phpcsFile->getTokens();
		$docCommentCloserPointer = $tokens[$docCommentOpenerPointer]['comment_closer'];

		$endOfLineBeforeFirstAnnotation = TokenHelper::findPreviousContent(
			$phpcsFile,
			T_DOC_COMMENT_WHITESPACE,
			$phpcsFile->eolChar,
			$firstAnnotation->getStartPointer() - 1,
			$docCommentOpenerPointer,
		);
		$docCommentContentEndPointer = TokenHelper::findNextContent(
			$phpcsFile,
			T_DOC_COMMENT_WHITESPACE,
			$phpcsFile->eolChar,
			$lastAnnotation->getEndPointer() + 1,
			$docCommentCloserPointer,
		);

		if ($docCommentContentEndPointer === null) {
			$docCommentContentEndPointer = $lastAnnotation->getEndPointer();
		}

		$phpcsFile->fixer->beginChangeset();

		if ($endOfLineBeforeFirstAnnotation === null) {
			FixerHelper::change(
				$phpcsFile,
				$docCommentOpenerPointer,
				$docCommentContentEndPointer,
				'/**' . $phpcsFile->eolChar . $fixedAnnotations,
			);
		} else {
			FixerHelper::change($phpcsFile, $endOfLineBeforeFirstAnnotation + 1, $docCommentContentEndPointer, $fixedAnnotations);
		}

		$phpcsFile->fixer->endChangeset();
	}

	/**
	 * @param list<Annotation> $annotations
	 * @return list<list<Annotation>>
	 */
	private function sortAnnotationsToGroups(array $annotations): array
	{
		$expectedAnnotationsGroups = $this->getAnnotationsGroups();

		$sortedAnnotationsGroups = [];
		$annotationsNotInAnyGroup = [];
		foreach ($annotations as $annotation) {
			foreach ($expectedAnnotationsGroups as $annotationsGroupPosition => $annotationsGroup) {
				foreach ($annotationsGroup as $annotationName) {
					if ($this->isAnnotationMatched($annotation, $annotationName)) {
						$sortedAnnotationsGroups[$annotationsGroupPosition][] = $annotation;
						continue 3;
					}
				}
			}

			$annotationsNotInAnyGroup[] = $annotation;
		}

		ksort($sortedAnnotationsGroups);

		foreach (array_keys($sortedAnnotationsGroups) as $annotationsGroupPosition) {
			$expectedAnnotationsGroupOrder = array_flip($expectedAnnotationsGroups[$annotationsGroupPosition]);
			usort(
				$sortedAnnotationsGroups[$annotationsGroupPosition],
				function (Annotation $firstAnnotation, Annotation $secondAnnotation) use ($expectedAnnotationsGroupOrder): int {
					$getExpectedOrder = function (string $annotationName) use ($expectedAnnotationsGroupOrder): int {
						if (array_key_exists($annotationName, $expectedAnnotationsGroupOrder)) {
							return $expectedAnnotationsGroupOrder[$annotationName];
						}

						$order = 0;
						foreach ($expectedAnnotationsGroupOrder as $expectedAnnotationName => $expectedAnnotationOrder) {
							if ($this->isAnnotationNameInAnnotationNamespace($expectedAnnotationName, $annotationName)) {
								$order = $expectedAnnotationOrder;
								break;
							}
						}

						return $order;
					};

					$expectedOrder = $getExpectedOrder($firstAnnotation->getName()) <=> $getExpectedOrder($secondAnnotation->getName());

					return $expectedOrder !== 0
						? $expectedOrder
						: $firstAnnotation->getStartPointer() <=> $secondAnnotation->getStartPointer();
				},
			);
		}

		if (count($annotationsNotInAnyGroup) > 0) {
			$sortedAnnotationsGroups[] = $annotationsNotInAnyGroup;
		}

		return array_values($sortedAnnotationsGroups);
	}

	private function isAnnotationNameInAnnotationNamespace(string $annotationNamespace, string $annotationName): bool
	{
		return $this->isAnnotationStartedFrom($annotationNamespace, $annotationName)
			|| (
				in_array(substr($annotationNamespace, -1), ['\\', '-', ':'], true)
				&& strpos($annotationName, $annotationNamespace) === 0
			);
	}

	private function isAnnotationStartedFrom(string $annotationNamespace, string $annotationName): bool
	{
		return substr($annotationNamespace, -1) === '*'
			&& strpos($annotationName, substr($annotationNamespace, 0, -1)) === 0;
	}

	private function isAnnotationMatched(Annotation $annotation, string $annotationName): bool
	{
		if ($annotation->getName() === $annotationName) {
			return true;
		}

		return $this->isAnnotationNameInAnnotationNamespace($annotationName, $annotation->getName());
	}

	private function checkLinesAfterLastContent(
		File $phpcsFile,
		int $docCommentOpenerPointer,
		int $docCommentCloserPointer,
		int $lastContentEndPointer
	): void
	{
		$whitespaceAfterLastContent = TokenHelper::getContent($phpcsFile, $lastContentEndPointer + 1, $docCommentCloserPointer);

		$linesCountAfterLastContent = max(substr_count($whitespaceAfterLastContent, $phpcsFile->eolChar) - 1, 0);
		if ($linesCountAfterLastContent === $this->linesCountAfterLastContent) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf(
				'Expected %d line%s after last content, found %d.',
				$this->linesCountAfterLastContent,
				$this->linesCountAfterLastContent === 1 ? '' : 's',
				$linesCountAfterLastContent,
			),
			$lastContentEndPointer,
			self::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTENT,
		);

		if (!$fix) {
			return;
		}

		$indentation = IndentationHelper::getIndentation($phpcsFile, $docCommentOpenerPointer);

		$phpcsFile->fixer->beginChangeset();

		FixerHelper::removeBetween($phpcsFile, $lastContentEndPointer, $docCommentCloserPointer);

		$phpcsFile->fixer->addNewline($lastContentEndPointer);

		for ($i = 1; $i <= $this->linesCountAfterLastContent; $i++) {
			$phpcsFile->fixer->addContent($lastContentEndPointer, sprintf('%s *%s', $indentation, $phpcsFile->eolChar));
		}

		$phpcsFile->fixer->addContentBefore($docCommentCloserPointer, $indentation . ' ');

		$phpcsFile->fixer->endChangeset();
	}

	/**
	 * @return array<list<string>>
	 */
	private function getAnnotationsGroups(): array
	{
		if ($this->normalizedAnnotationsGroups === null) {
			$this->normalizedAnnotationsGroups = [];
			foreach ($this->annotationsGroups as $annotationsGroup) {
				$this->normalizedAnnotationsGroups[] = SniffSettingsHelper::normalizeArray(explode(',', $annotationsGroup));
			}
		}

		return $this->normalizedAnnotationsGroups;
	}

}
