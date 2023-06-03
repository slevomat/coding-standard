<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHPStan\PhpDocParser\Ast\AbstractNodeVisitor;
use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\NodeTraverser;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\AnnotationTypeHelper;
use SlevomatCodingStandard\Helpers\DocCommentHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\TypeHintHelper;
use function array_flip;
use function array_key_exists;
use function array_map;
use function count;
use function in_array;
use function sprintf;
use const T_DOC_COMMENT_OPEN_TAG;
use const T_FUNCTION;

class DisallowArrayTypeHintSyntaxSniff implements Sniff
{

	public const CODE_DISALLOWED_ARRAY_TYPE_HINT_SYNTAX = 'DisallowedArrayTypeHintSyntax';

	/** @var list<string> */
	public $traversableTypeHints = [];

	/** @var array<string, int>|null */
	private $normalizedTraversableTypeHints;

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
			$arrayTypeNodes = $this->getArrayTypeNodes($annotation->getValue());

			foreach ($arrayTypeNodes as $arrayTypeNode) {
				$fix = $phpcsFile->addFixableError(
					sprintf(
						'Usage of array type hint syntax in "%s" is disallowed, use generic type hint syntax instead.',
						AnnotationTypeHelper::print($arrayTypeNode)
					),
					$annotation->getStartPointer(),
					self::CODE_DISALLOWED_ARRAY_TYPE_HINT_SYNTAX
				);

				if (!$fix) {
					continue;
				}

				$parsedDocComment = DocCommentHelper::parseDocComment($phpcsFile, $docCommentOpenPointer);

				/** @var list<UnionTypeNode> $unionTypeNodes */
				$unionTypeNodes = AnnotationHelper::getAnnotationNodesByType($annotation->getNode(), UnionTypeNode::class);

				$unionTypeNode = $this->findUnionTypeThatContainsArrayType($arrayTypeNode, $unionTypeNodes);

				if ($unionTypeNode !== null) {
					$genericIdentifier = $this->findGenericIdentifier(
						$phpcsFile,
						$docCommentOpenPointer,
						$unionTypeNode,
						$annotation->getValue()
					);
					if ($genericIdentifier !== null) {
						$genericTypeNode = new GenericTypeNode(
							new IdentifierTypeNode($genericIdentifier),
							[$this->fixArrayNode($arrayTypeNode->type)]
						);
						$fixedDocComment = AnnotationHelper::fixAnnotation(
							$parsedDocComment,
							$annotation,
							$unionTypeNode,
							$genericTypeNode
						);
					} else {
						$genericTypeNode = new GenericTypeNode(
							new IdentifierTypeNode('array'),
							[$this->fixArrayNode($arrayTypeNode->type)]
						);
						$fixedDocComment = AnnotationHelper::fixAnnotation(
							$parsedDocComment,
							$annotation,
							$arrayTypeNode,
							$genericTypeNode
						);
					}
				} else {
					$genericIdentifier = $this->findGenericIdentifier(
						$phpcsFile,
						$docCommentOpenPointer,
						$arrayTypeNode,
						$annotation->getValue()
					) ?? 'array';

					$genericTypeNode = new GenericTypeNode(
						new IdentifierTypeNode($genericIdentifier),
						[$this->fixArrayNode($arrayTypeNode->type)]
					);
					$fixedDocComment = AnnotationHelper::fixAnnotation($parsedDocComment, $annotation, $arrayTypeNode, $genericTypeNode);
				}

				$phpcsFile->fixer->beginChangeset();

				FixerHelper::change(
					$phpcsFile,
					$parsedDocComment->getOpenPointer(),
					$parsedDocComment->getClosePointer(),
					$fixedDocComment
				);

				$phpcsFile->fixer->endChangeset();
			}
		}
	}

	/**
	 * @return list<ArrayTypeNode>
	 */
	public function getArrayTypeNodes(Node $node): array
	{
		static $visitor;
		static $traverser;

		if ($visitor === null) {
			$visitor = new class extends AbstractNodeVisitor {

				/** @var list<ArrayTypeNode> */
				private $nodes = [];

				/**
				 * @return Node|list<Node>|NodeTraverser::*|null
				 */
				public function enterNode(Node $node)
				{
					if ($node instanceof ArrayTypeNode) {
						$this->nodes[] = $node;

						if ($node->type instanceof ArrayTypeNode) {
							return NodeTraverser::DONT_TRAVERSE_CHILDREN;
						}
					}

					return null;
				}

				public function cleanNodes(): void
				{
					$this->nodes = [];
				}

				/**
				 * @return list<ArrayTypeNode>
				 */
				public function getNodes(): array
				{
					return $this->nodes;
				}

			};
		}

		if ($traverser === null) {
			$traverser = new NodeTraverser([$visitor]);
		}

		$visitor->cleanNodes();

		$traverser->traverse([$node]);

		return $visitor->getNodes();
	}

	private function fixArrayNode(TypeNode $node): TypeNode
	{
		if (!$node instanceof ArrayTypeNode) {
			return $node;
		}

		return new GenericTypeNode(new IdentifierTypeNode('array'), [$this->fixArrayNode($node->type)]);
	}

	/**
	 * @param list<UnionTypeNode> $unionTypeNodes
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

	private function findGenericIdentifier(
		File $phpcsFile,
		int $docCommentOpenPointer,
		TypeNode $typeNode,
		PhpDocTagValueNode $annotationValue
	): ?string
	{
		if (!$typeNode instanceof UnionTypeNode) {
			if (!$annotationValue instanceof ParamTagValueNode && !$annotationValue instanceof ReturnTagValueNode) {
				return null;
			}

			$functionPointer = TokenHelper::findNext($phpcsFile, TokenHelper::$functionTokenCodes, $docCommentOpenPointer + 1);

			if ($functionPointer === null || $phpcsFile->getTokens()[$functionPointer]['code'] !== T_FUNCTION) {
				return null;
			}

			if ($annotationValue instanceof ParamTagValueNode) {
				$parameterTypeHints = FunctionHelper::getParametersTypeHints($phpcsFile, $functionPointer);
				return array_key_exists(
					$annotationValue->parameterName,
					$parameterTypeHints
				) && $parameterTypeHints[$annotationValue->parameterName] !== null
					? $parameterTypeHints[$annotationValue->parameterName]->getTypeHint()
					: null;
			}

			$returnType = FunctionHelper::findReturnTypeHint($phpcsFile, $functionPointer);
			return $returnType !== null ? $returnType->getTypeHint() : null;
		}

		if (count($typeNode->types) !== 2) {
			return null;
		}

		if (
			$typeNode->types[0] instanceof ArrayTypeNode
			&& $typeNode->types[1] instanceof IdentifierTypeNode
			&& $this->isTraversableType(
				TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $docCommentOpenPointer, $typeNode->types[1]->name)
			)
		) {
			return $typeNode->types[1]->name;
		}

		if (
			$typeNode->types[1] instanceof ArrayTypeNode
			&& $typeNode->types[0] instanceof IdentifierTypeNode
			&& $this->isTraversableType(
				TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $docCommentOpenPointer, $typeNode->types[0]->name)
			)
		) {
			return $typeNode->types[0]->name;
		}

		return null;
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
			$this->normalizedTraversableTypeHints = array_flip(array_map(static function (string $typeHint): string {
				return NamespaceHelper::isFullyQualifiedName($typeHint)
					? $typeHint
					: sprintf('%s%s', NamespaceHelper::NAMESPACE_SEPARATOR, $typeHint);
			}, SniffSettingsHelper::normalizeArray($this->traversableTypeHints)));
		}
		return $this->normalizedTraversableTypeHints;
	}

}
