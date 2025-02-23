<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstFetchNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\DocCommentHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\PhpDocParserHelper;
use SlevomatCodingStandard\Helpers\ReferencedName;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TypeHelper;
use SlevomatCodingStandard\Helpers\TypeHintHelper;
use function in_array;
use function sprintf;
use function strtolower;
use const T_DOC_COMMENT_OPEN_TAG;

class FullyQualifiedClassNameInAnnotationSniff implements Sniff
{

	public const CODE_NON_FULLY_QUALIFIED_CLASS_NAME = 'NonFullyQualifiedClassName';

	/** @var list<string> */
	public array $ignoredAnnotationNames = [];

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
	 * @param int $docCommentOpenPointer
	 */
	public function process(File $phpcsFile, $docCommentOpenPointer): void
	{
		$annotations = AnnotationHelper::getAnnotations($phpcsFile, $docCommentOpenPointer);
		$this->ignoredAnnotationNames = SniffSettingsHelper::normalizeArray($this->ignoredAnnotationNames);

		foreach ($annotations as $annotation) {
			$identifierTypeNodes = AnnotationHelper::getAnnotationNodesByType($annotation->getNode(), IdentifierTypeNode::class);

			$annotationName = $annotation->getName();

			foreach ($identifierTypeNodes as $typeHintNode) {
				$typeHint = $typeHintNode->name;

				$lowercasedTypeHint = strtolower($typeHint);
				if (
					TypeHintHelper::isSimpleTypeHint($lowercasedTypeHint)
					|| TypeHintHelper::isSimpleUnofficialTypeHints($lowercasedTypeHint)
					|| !TypeHelper::isTypeName($typeHint)
					|| TypeHintHelper::isTypeDefinedInAnnotation($phpcsFile, $docCommentOpenPointer, $typeHint)
				) {
					continue;
				}

				if (in_array($annotationName, $this->ignoredAnnotationNames, true)) {
					continue;
				}

				$fullyQualifiedTypeHint = TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $docCommentOpenPointer, $typeHint);
				if ($fullyQualifiedTypeHint === $typeHint) {
					continue;
				}

				$fix = $phpcsFile->addFixableError(sprintf(
					'Class name %s in %s should be referenced via a fully qualified name.',
					$fullyQualifiedTypeHint,
					$annotationName,
				), $annotation->getStartPointer(), self::CODE_NON_FULLY_QUALIFIED_CLASS_NAME);

				if (!$fix) {
					continue;
				}

				$parsedDocComment = DocCommentHelper::parseDocComment($phpcsFile, $docCommentOpenPointer);

				$fixedDocComment = AnnotationHelper::fixAnnotation(
					$parsedDocComment,
					$annotation,
					$typeHintNode,
					new IdentifierTypeNode($fullyQualifiedTypeHint),
				);

				$phpcsFile->fixer->beginChangeset();

				FixerHelper::change(
					$phpcsFile,
					$parsedDocComment->getOpenPointer(),
					$parsedDocComment->getClosePointer(),
					$fixedDocComment,
				);

				$phpcsFile->fixer->endChangeset();
			}

			$constantFetchNodes = AnnotationHelper::getAnnotationNodesByType($annotation->getNode(), ConstFetchNode::class);

			foreach ($constantFetchNodes as $constantFetchNode) {
				$isClassConstant = $constantFetchNode->className !== '';

				$typeHint = $isClassConstant
					? $constantFetchNode->className
					: $constantFetchNode->name;

				if ($typeHint === 'self') {
					continue;
				}

				$fullyQualifiedTypeHint = $isClassConstant
					? NamespaceHelper::resolveClassName($phpcsFile, $typeHint, $docCommentOpenPointer)
					: NamespaceHelper::resolveName($phpcsFile, $typeHint, ReferencedName::TYPE_CONSTANT, $docCommentOpenPointer);

				if ($fullyQualifiedTypeHint === $typeHint) {
					continue;
				}

				$fix = $phpcsFile->addFixableError(sprintf(
					'%s name %s in %s should be referenced via a fully qualified name.',
					$isClassConstant ? 'Class' : 'Constant',
					$fullyQualifiedTypeHint,
					$annotationName,
				), $annotation->getStartPointer(), self::CODE_NON_FULLY_QUALIFIED_CLASS_NAME);

				if (!$fix) {
					continue;
				}

				$fixedConstantFetchNode = PhpDocParserHelper::cloneNode($constantFetchNode);
				if ($isClassConstant) {
					$fixedConstantFetchNode->className = $fullyQualifiedTypeHint;
				} else {
					$fixedConstantFetchNode->name = $fullyQualifiedTypeHint;
				}

				$parsedDocComment = DocCommentHelper::parseDocComment($phpcsFile, $docCommentOpenPointer);

				$fixedDocComment = AnnotationHelper::fixAnnotation(
					$parsedDocComment,
					$annotation,
					$constantFetchNode,
					$fixedConstantFetchNode,
				);

				$phpcsFile->fixer->beginChangeset();

				FixerHelper::change(
					$phpcsFile,
					$parsedDocComment->getOpenPointer(),
					$parsedDocComment->getClosePointer(),
					$fixedDocComment,
				);

				$phpcsFile->fixer->endChangeset();

			}
		}
	}

}
