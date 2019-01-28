<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use SlevomatCodingStandard\Helpers\Annotation\GenericAnnotation;
use SlevomatCodingStandard\Helpers\Annotation\MethodAnnotation;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use function count;
use function strtolower;
use const T_DOC_COMMENT_OPEN_TAG;

class NullTypeHintOnLastPositionSniff implements Sniff
{

	public const CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION = 'NullTypeHintNotOnLastPosition';

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
	public function process(File $phpcsFile, $docCommentOpenPointer): void
	{
		$annotations = AnnotationHelper::getAnnotations($phpcsFile, $docCommentOpenPointer);

		foreach ($annotations as $annotationByName) {
			/** @var \SlevomatCodingStandard\Helpers\Annotation\VariableAnnotation|\SlevomatCodingStandard\Helpers\Annotation\ParameterAnnotation|\SlevomatCodingStandard\Helpers\Annotation\ReturnAnnotation|\SlevomatCodingStandard\Helpers\Annotation\ThrowsAnnotation|\SlevomatCodingStandard\Helpers\Annotation\PropertyAnnotation|\SlevomatCodingStandard\Helpers\Annotation\MethodAnnotation|\SlevomatCodingStandard\Helpers\Annotation\GenericAnnotation $annotation */
			foreach ($annotationByName as $annotation) {
				if ($annotation instanceof GenericAnnotation) {
					continue;
				}

				if ($annotation->getContent() === null) {
					continue;
				}

				if ($annotation->isInvalid()) {
					continue;
				}

				$annotationTypes = [];

				if ($annotation instanceof MethodAnnotation) {
					if ($annotation->getMethodReturnType() !== null) {
						$annotationTypes[] = $annotation->getMethodReturnType();
					}
				} else {
					$annotationTypes[] = $annotation->getType();
				}

				foreach ($annotationTypes as $annotationType) {
					if (!$annotationType instanceof UnionTypeNode) {
						continue;
					}

					$nullTypeNode = null;
					$nullPosition = 0;
					$position = 0;
					foreach ($annotationType->types as $typeNode) {
						if ($typeNode instanceof IdentifierTypeNode && strtolower($typeNode->name) === 'null') {
							$nullTypeNode = $typeNode;
							$nullPosition = $position;
							break;
						}

						$position++;
					}

					if ($nullTypeNode === null) {
						continue;
					}

					if ($nullPosition === count($annotationType->types) - 1) {
						continue;
					}

					$fix = $phpcsFile->addFixableError(
						'Null type hint should be on last position.',
						$annotation->getStartPointer(),
						self::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION
					);

					if (!$fix) {
						continue;
					}

					$phpcsFile->fixer->beginChangeset();

					for ($i = $annotation->getStartPointer(); $i <= $annotation->getEndPointer(); $i++) {
						$phpcsFile->fixer->replaceToken($i, '');
					}

					$fixedTypeNodes = [];
					foreach ($annotationType->types as $typeNode) {
						if ($typeNode === $nullTypeNode) {
							continue;
						}

						$fixedTypeNodes[] = $typeNode;
					}
					$fixedTypeNodes[] = $nullTypeNode;
					$fixedAnnotationType = new UnionTypeNode($fixedTypeNodes);

					$fixedAnnotationContent = AnnotationHelper::fixAnnotation($phpcsFile, $annotation, $annotationType, $fixedAnnotationType);

					$phpcsFile->fixer->replaceToken($annotation->getStartPointer(), $fixedAnnotationContent);
					for ($i = $annotation->getStartPointer() + 1; $i <= $annotation->getEndPointer(); $i++) {
						$phpcsFile->fixer->replaceToken($i, '');
					}

					$phpcsFile->fixer->endChangeset();
				}
			}
		}
	}

}
