<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\AnnotationTypeHelper;
use SlevomatCodingStandard\Helpers\TypeHelper;
use SlevomatCodingStandard\Helpers\TypeHintHelper;
use function in_array;
use function sprintf;
use function strtolower;
use const T_DOC_COMMENT_OPEN_TAG;

class FullyQualifiedClassNameInAnnotationSniff implements Sniff
{

	public const CODE_NON_FULLY_QUALIFIED_CLASS_NAME = 'NonFullyQualifiedClassName';

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

		foreach ($annotations as $annotationName => $annotationByName) {
			if (!in_array($annotationName, ['@var', '@param', '@return', '@throws'], true)) {
				continue;
			}

			/** @var \SlevomatCodingStandard\Helpers\Annotation\VariableAnnotation|\SlevomatCodingStandard\Helpers\Annotation\ParameterAnnotation|\SlevomatCodingStandard\Helpers\Annotation\ReturnAnnotation|\SlevomatCodingStandard\Helpers\Annotation\ThrowsAnnotation $annotation */
			foreach ($annotationByName as $annotation) {
				if ($annotation->getContent() === null) {
					continue;
				}

				if ($annotation->isInvalid()) {
					continue;
				}

				$annotationType = $annotation->getType();
				$typeNodes = $annotationType instanceof UnionTypeNode ? $annotationType->types : [$annotationType];

				foreach ($typeNodes as $typeNode) {
					$typeHintNode = AnnotationTypeHelper::getTypeHintNode($typeNode);
					$typeHint = AnnotationTypeHelper::getTypeHintFromNode($typeHintNode);

					$lowercasedTypeHint = strtolower($typeHint);
					if (
						TypeHintHelper::isSimpleTypeHint($lowercasedTypeHint)
						|| TypeHintHelper::isSimpleUnofficialTypeHints($lowercasedTypeHint)
						|| !TypeHelper::isTypeName($typeHint)
					) {
						continue;
					}

					$fullyQualifiedTypeHint = TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $annotation->getStartPointer(), $typeHint);
					if ($fullyQualifiedTypeHint === $typeHint) {
						continue;
					}
					$fix = $phpcsFile->addFixableError(sprintf(
						'Class name %s in %s should be referenced via a fully qualified name.',
						$fullyQualifiedTypeHint,
						$annotationName
					), $annotation->getStartPointer(), self::CODE_NON_FULLY_QUALIFIED_CLASS_NAME);

					if (!$fix) {
						continue;
					}

					$phpcsFile->fixer->beginChangeset();

					$fixedAnnotationContent = AnnotationHelper::fixAnnotation($phpcsFile, $annotation, $typeHintNode, $fullyQualifiedTypeHint);

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
