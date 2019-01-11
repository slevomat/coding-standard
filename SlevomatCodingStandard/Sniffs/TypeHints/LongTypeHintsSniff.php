<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\AnnotationTypeHelper;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\PropertyHelper;
use function sprintf;
use function strtolower;
use const T_FUNCTION;
use const T_VARIABLE;

class LongTypeHintsSniff implements Sniff
{

	public const CODE_USED_LONG_TYPE_HINT = 'UsedLongTypeHint';

	/**
	 * @return mixed[]
	 */
	public function register(): array
	{
		return [
			T_FUNCTION,
			T_VARIABLE,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $pointer
	 */
	public function process(File $phpcsFile, $pointer): void
	{
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$pointer]['code'] === T_FUNCTION) {
			$allAnnotations = ['@param' => FunctionHelper::getParametersAnnotations($phpcsFile, $pointer)];

			$return = FunctionHelper::findReturnAnnotation($phpcsFile, $pointer);
			if ($return !== null) {
				$allAnnotations['@return'] = [$return];
			}
		} else {
			if (!PropertyHelper::isProperty($phpcsFile, $pointer)) {
				return;
			}

			$allAnnotations = ['@var' => AnnotationHelper::getAnnotationsByName($phpcsFile, $pointer, '@var')];
		}

		foreach ($allAnnotations as $annotationName => $annotations) {
			/** @var \SlevomatCodingStandard\Helpers\Annotation\ParameterAnnotation|\SlevomatCodingStandard\Helpers\Annotation\ReturnAnnotation|\SlevomatCodingStandard\Helpers\Annotation\VariableAnnotation $annotation */
			foreach ($annotations as $annotation) {
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

					$shortTypeHint = null;
					if ($lowercasedTypeHint === 'integer') {
						$shortTypeHint = 'int';
					} elseif ($lowercasedTypeHint === 'boolean') {
						$shortTypeHint = 'bool';
					}

					if ($shortTypeHint === null) {
						continue;
					}

					$fix = $phpcsFile->addFixableError(sprintf(
						'Expected "%s" but found "%s" in %s annotation.',
						$shortTypeHint,
						$typeHint,
						$annotationName
					), $annotation->getStartPointer(), self::CODE_USED_LONG_TYPE_HINT);

					if (!$fix) {
						continue;
					}

					$phpcsFile->fixer->beginChangeset();

					$fixedAnnotationContent = AnnotationHelper::fixAnnotation($phpcsFile, $annotation, $typeHintNode, $shortTypeHint);

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
