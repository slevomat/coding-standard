<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;
use PHPStan\PhpDocParser\Ast\PhpDoc\GenericTagValueNode;
use function array_reduce;
use function assert;
use function explode;
use function sprintf;
use function strpos;
use const T_DOC_COMMENT_CLOSE_TAG;
use const T_DOC_COMMENT_OPEN_TAG;
use const T_DOC_COMMENT_STAR;

/**
 * @internal
 */
class SuppressHelper
{

	public const ANNOTATION = '@phpcsSuppress';

	public static function isSniffSuppressed(File $phpcsFile, int $pointer, string $suppressName): bool
	{
		/** @var list<Annotation<GenericTagValueNode>> $annotations */
		$annotations = AnnotationHelper::getAnnotations($phpcsFile, $pointer, self::ANNOTATION);

		return array_reduce(
			$annotations,
			static function (bool $carry, Annotation $annotation) use ($suppressName): bool {
				$annotationSuppressName = explode(' ', $annotation->getValue()->value)[0];

				if (
					$suppressName === $annotationSuppressName
					|| strpos($suppressName, sprintf('%s.', $annotationSuppressName)) === 0
				) {
					$carry = true;
				}

				return $carry;
			},
			false,
		);
	}

	public static function removeSuppressAnnotation(File $phpcsFile, int $pointer, string $suppressName): void
	{
		$suppressAnnotation = null;
		/** @var Annotation<GenericTagValueNode> $annotation */
		foreach (AnnotationHelper::getAnnotations($phpcsFile, $pointer, self::ANNOTATION) as $annotation) {
			if ($annotation->getValue()->value === $suppressName) {
				$suppressAnnotation = $annotation;
				break;
			}
		}

		assert($suppressAnnotation !== null);

		$tokens = $phpcsFile->getTokens();

		/** @var int $pointerBefore */
		$pointerBefore = TokenHelper::findPrevious(
			$phpcsFile,
			[T_DOC_COMMENT_OPEN_TAG, T_DOC_COMMENT_STAR],
			$suppressAnnotation->getStartPointer() - 1,
		);

		$changeStart = $tokens[$pointerBefore]['code'] === T_DOC_COMMENT_STAR ? $pointerBefore : $suppressAnnotation->getStartPointer();

		/** @var int $changeEnd */
		$changeEnd = TokenHelper::findNext(
			$phpcsFile,
			[T_DOC_COMMENT_CLOSE_TAG, T_DOC_COMMENT_STAR],
			$suppressAnnotation->getEndPointer() + 1,
		) - 1;

		$phpcsFile->fixer->beginChangeset();
		FixerHelper::removeBetweenIncluding($phpcsFile, $changeStart, $changeEnd);
		$phpcsFile->fixer->endChangeset();
	}

}
