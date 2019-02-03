<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use SlevomatCodingStandard\Helpers\Annotation\GenericAnnotation;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\AnnotationTypeHelper;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TypeHintHelper;
use function array_flip;
use function array_key_exists;
use function array_map;
use function count;
use function in_array;
use function sprintf;
use const T_DOC_COMMENT_OPEN_TAG;

class DisallowArrayTypeHintSyntaxSniff implements Sniff
{

	public const CODE_DISALLOWED_ARRAY_TYPE_HINT_SYNTAX = 'DisallowedArrayTypeHintSyntax';

	/** @var string[] */
	public $traversableTypeHints = [];

	/** @var array<string, int>|null */
	private $normalizedTraversableTypeHints;

	/**
	 * @return (int|string)[]
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
			foreach ($annotationByName as $annotation) {
				if ($annotation instanceof GenericAnnotation) {
					continue;
				}

				if ($annotation->isInvalid()) {
					continue;
				}

				foreach (AnnotationHelper::getAnnotationTypes($annotation) as $annotationType) {
					$unionTypeNodes = AnnotationTypeHelper::getUnionTypeNodes($annotationType);
					foreach ($this->getArrayTypeNodes($annotationType) as $arrayTypeNode) {
						$fix = $phpcsFile->addFixableError(
							sprintf('Usage of array type hint syntax in "%s" is disallowed, use generic type hint syntax instead.', AnnotationTypeHelper::export($arrayTypeNode)),
							$annotation->getStartPointer(),
							self::CODE_DISALLOWED_ARRAY_TYPE_HINT_SYNTAX
						);

						if (!$fix) {
							continue;
						}

						$unionTypeNode = $this->findUnionTypeThatContainsArrayType($arrayTypeNode, $unionTypeNodes);

						if ($unionTypeNode !== null) {
							$genericIdentifier = $this->findGenericIdentifier($phpcsFile, $annotation->getStartPointer(), $unionTypeNode);
							if ($genericIdentifier !== null) {
								$genericTypeNode = new GenericTypeNode(new IdentifierTypeNode($genericIdentifier), [$arrayTypeNode->type]);
								$fixedAnnotationContent = AnnotationHelper::fixAnnotation($phpcsFile, $annotation, $unionTypeNode, $genericTypeNode);
							} else {
								$genericTypeNode = new GenericTypeNode(new IdentifierTypeNode('array'), [$arrayTypeNode->type]);
								$fixedAnnotationContent = AnnotationHelper::fixAnnotation($phpcsFile, $annotation, $arrayTypeNode, $genericTypeNode);
							}
						} else {
							$genericTypeNode = new GenericTypeNode(new IdentifierTypeNode('array'), [$arrayTypeNode->type]);
							$fixedAnnotationContent = AnnotationHelper::fixAnnotation($phpcsFile, $annotation, $arrayTypeNode, $genericTypeNode);
						}

						$phpcsFile->fixer->beginChangeset();
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

	/**
	 * @param \PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode $arrayTypeNode
	 * @param \PHPStan\PhpDocParser\Ast\Type\UnionTypeNode[] $unionTypeNodes
	 * @return \PHPStan\PhpDocParser\Ast\Type\UnionTypeNode|null
	 */
	private function findUnionTypeThatContainsArrayType(ArrayTypeNode $arrayTypeNode, array $unionTypeNodes): ?UnionTypeNode
	{
		foreach ($unionTypeNodes as $unionTypeNode) {
			if (in_array($arrayTypeNode, $unionTypeNode->types, true)) {
				return $unionTypeNode;
			}
		}

		return null;
	}

	private function findGenericIdentifier(File $phpcsFile, int $pointer, UnionTypeNode $unionTypeNode): ?string
	{
		if (count($unionTypeNode->types) !== 2) {
			return null;
		}

		if (
			$unionTypeNode->types[0] instanceof ArrayTypeNode
			&& $unionTypeNode->types[1] instanceof IdentifierTypeNode
			&& $this->isTraversableType(TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $pointer, $unionTypeNode->types[1]->name))
		) {
			return $unionTypeNode->types[1]->name;
		}

		if (
			$unionTypeNode->types[1] instanceof ArrayTypeNode
			&& $unionTypeNode->types[0] instanceof IdentifierTypeNode
			&& $this->isTraversableType(TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $pointer, $unionTypeNode->types[0]->name))
		) {
			return $unionTypeNode->types[0]->name;
		}

		return null;
	}

	/**
	 * @param \PHPStan\PhpDocParser\Ast\Type\TypeNode $typeNode
	 * @return \PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode[]
	 */
	public function getArrayTypeNodes(TypeNode $typeNode): array
	{
		$arrayTypeNodes = AnnotationTypeHelper::getArrayTypeNodes($typeNode);

		$arrayTypeNodesToIgnore = [];
		foreach ($arrayTypeNodes as $arrayTypeNode) {
			if (!($arrayTypeNode->type instanceof ArrayTypeNode)) {
				continue;
			}

			$arrayTypeNodesToIgnore[] = $arrayTypeNode->type;
		}

		foreach ($arrayTypeNodes as $no => $arrayTypeNode) {
			if (!in_array($arrayTypeNode, $arrayTypeNodesToIgnore, true)) {
				continue;
			}

			unset($arrayTypeNodes[$no]);
		}

		return $arrayTypeNodes;
	}

	private function isTraversableType(string $type): bool
	{
		return TypeHintHelper::isSimpleIterableTypeHint($type) || array_key_exists($type, $this->getNormalizedTraversableTypeHints());
	}

	/**
	 * @return array<string, int>
	 */
	private function getNormalizedTraversableTypeHints(): array
	{
		if ($this->normalizedTraversableTypeHints === null) {
			$this->normalizedTraversableTypeHints = array_flip(array_map(function (string $typeHint): string {
				return NamespaceHelper::isFullyQualifiedName($typeHint) ? $typeHint : sprintf('%s%s', NamespaceHelper::NAMESPACE_SEPARATOR, $typeHint);
			}, SniffSettingsHelper::normalizeArray($this->traversableTypeHints)));
		}
		return $this->normalizedTraversableTypeHints;
	}

}
