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
use SlevomatCodingStandard\Helpers\TypeHelper;
use SlevomatCodingStandard\Helpers\TypeHintHelper;
use function sprintf;
use function strtolower;
use const T_DOC_COMMENT_OPEN_TAG;

class FullyQualifiedClassNameInAnnotationSniff implements Sniff
{

	public const CODE_NON_FULLY_QUALIFIED_CLASS_NAME = 'NonFullyQualifiedClassName';

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

		foreach ($annotations as $annotation) {
			/** @var list<IdentifierTypeNode> $identifierTypeNodes */
			$identifierTypeNodes = AnnotationHelper::getAnnotationNodesByType($annotation->getNode(), IdentifierTypeNode::class);

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

				$fullyQualifiedTypeHint = TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $docCommentOpenPointer, $typeHint);
				if ($fullyQualifiedTypeHint === $typeHint) {
					continue;
				}

				$fix = $phpcsFile->addFixableError(sprintf(
					'Class name %s in %s should be referenced via a fully qualified name.',
					$fullyQualifiedTypeHint,
					$annotation->getName()
				), $annotation->getStartPointer(), self::CODE_NON_FULLY_QUALIFIED_CLASS_NAME);

				if (!$fix) {
					continue;
				}

				$parsedDocComment = DocCommentHelper::parseDocComment($phpcsFile, $docCommentOpenPointer);

				$fixedDocComment = AnnotationHelper::fixAnnotation(
					$parsedDocComment,
					$annotation,
					$typeHintNode,
					new IdentifierTypeNode($fullyQualifiedTypeHint)
				);

				$phpcsFile->fixer->beginChangeset();

				$phpcsFile->fixer->replaceToken($parsedDocComment->getOpenPointer(), $fixedDocComment);

				FixerHelper::removeBetweenIncluding(
					$phpcsFile,
					$parsedDocComment->getOpenPointer() + 1,
					$parsedDocComment->getClosePointer()
				);

				$phpcsFile->fixer->endChangeset();
			}

			/** @var list<ConstFetchNode> $constantFetchNodes */
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
					$annotation->getName()
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
					$fixedConstantFetchNode
				);

				$phpcsFile->fixer->beginChangeset();

				$phpcsFile->fixer->replaceToken($parsedDocComment->getOpenPointer(), $fixedDocComment);

				FixerHelper::removeBetweenIncluding(
					$phpcsFile,
					$parsedDocComment->getOpenPointer() + 1,
					$parsedDocComment->getClosePointer()
				);

				$phpcsFile->fixer->endChangeset();

			}
		}
	}

}
