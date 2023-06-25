<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;
use PHPStan\PhpDocParser\Ast\AbstractNodeVisitor;
use PHPStan\PhpDocParser\Ast\Attribute;
use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\NodeTraverser;
use PHPStan\PhpDocParser\Ast\PhpDoc\Doctrine\DoctrineArgument;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\TypelessParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayShapeItemNode;
use PHPStan\PhpDocParser\Ast\Type\CallableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\ConditionalTypeForParameterNode;
use PHPStan\PhpDocParser\Ast\Type\ConditionalTypeNode;
use PHPStan\PhpDocParser\Ast\Type\ConstTypeNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IntersectionTypeNode;
use PHPStan\PhpDocParser\Ast\Type\ObjectShapeItemNode;
use PHPStan\PhpDocParser\Ast\Type\ObjectShapeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use function count;
use function in_array;
use function sprintf;
use function strlen;
use function strtolower;
use const T_DOC_COMMENT_STRING;

/**
 * @internal
 */
class AnnotationHelper
{

	public const STATIC_ANALYSIS_PREFIXES = ['psalm', 'phpstan'];

	/**
	 * @return list<Annotation>
	 */
	public static function getAnnotations(File $phpcsFile, int $pointer, ?string $name = null): array
	{
		$docCommentOpenPointer = DocCommentHelper::findDocCommentOpenPointer($phpcsFile, $pointer);
		if ($docCommentOpenPointer === null) {
			return [];
		}

		return SniffLocalCache::getAndSetIfNotCached(
			$phpcsFile,
			sprintf('annotations-%d-%s', $docCommentOpenPointer, $name ?? 'all'),
			static function () use ($phpcsFile, $docCommentOpenPointer, $name): array {
				$annotations = [];

				if ($name !== null) {
					foreach (self::getAnnotations($phpcsFile, $docCommentOpenPointer) as $annotation) {
						if ($annotation->getName() === $name) {
							$annotations[] = $annotation;
						}
					}
				} else {
					$parsedDocComment = DocCommentHelper::parseDocComment($phpcsFile, $docCommentOpenPointer);

					if ($parsedDocComment !== null) {
						foreach ($parsedDocComment->getNode()->getTags() as $node) {
							$annotationStartPointer = self::getStartPointer($phpcsFile, $parsedDocComment->getOpenPointer(), $node);
							$annotations[] = new Annotation(
								$node,
								$annotationStartPointer,
								self::getEndPointer($phpcsFile, $parsedDocComment, $annotationStartPointer, $node)
							);
						}
					}
				}

				return $annotations;
			}
		);
	}

	/**
	 * @param class-string $type
	 * @return list<Node>
	 */
	public static function getAnnotationNodesByType(Node $node, string $type): array
	{
		static $visitor;
		static $traverser;

		if ($visitor === null) {
			$visitor = new class extends AbstractNodeVisitor {

				/** @var class-string */
				private $type;

				/** @var list<Node> */
				private $nodes = [];

				/** @var list<Node> */
				private $nodesToIgnore = [];

				/**
				 * @return Node|list<Node>|NodeTraverser::*|null
				 */
				public function enterNode(Node $node)
				{
					if ($this->type === IdentifierTypeNode::class) {
						if ($node instanceof ArrayShapeItemNode || $node instanceof ObjectShapeItemNode) {
							$this->nodesToIgnore[] = $node->keyName;
						} elseif ($node instanceof DoctrineArgument) {
							$this->nodesToIgnore[] = $node->key;
						}
					}

					if ($node instanceof $this->type && !in_array($node, $this->nodesToIgnore, true)) {
						$this->nodes[] = $node;
					}

					return null;
				}

				/**
				 * @param class-string $type
				 */
				public function setType(string $type): void
				{
					$this->type = $type;
				}

				public function clean(): void
				{
					$this->nodes = [];
					$this->nodesToIgnore = [];
				}

				/**
				 * @return list<Node>
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

		$visitor->setType($type);
		$visitor->clean();

		$traverser->traverse([$node]);

		return $visitor->getNodes();
	}

	public static function fixAnnotation(
		ParsedDocComment $parsedDocComment,
		Annotation $annotation,
		Node $nodeToFix,
		Node $fixedNode
	): string
	{
		$originalNode = $annotation->getNode();

		/** @var PhpDocNode $newPhpDocNode */
		$newPhpDocNode = PhpDocParserHelper::cloneNode($parsedDocComment->getNode());

		foreach ($newPhpDocNode->getTags() as $node) {
			if ($node->getAttribute(Attribute::ORIGINAL_NODE) === $originalNode) {
				self::changeAnnotationNode($node, $nodeToFix, $fixedNode);
				break;
			}
		}

		return PhpDocParserHelper::getPrinter()->printFormatPreserving(
			$newPhpDocNode,
			$parsedDocComment->getNode(),
			$parsedDocComment->getTokens()
		);
	}

	/**
	 * @param array<int, string> $traversableTypeHints
	 */
	public static function isAnnotationUseless(
		File $phpcsFile,
		int $functionPointer,
		?TypeHint $typeHint,
		Annotation $annotation,
		array $traversableTypeHints,
		bool $enableUnionTypeHint = false,
		bool $enableIntersectionTypeHint = false,
		bool $enableStandaloneNullTrueFalseTypeHints = false
	): bool
	{
		if ($annotation->isInvalid()) {
			return false;
		}

		if ($typeHint === null) {
			return false;
		}

		/** @var ParamTagValueNode|TypelessParamTagValueNode|ReturnTagValueNode|VarTagValueNode $annotationValue */
		$annotationValue = $annotation->getValue();

		if ($annotationValue->description !== '') {
			return false;
		}

		if ($annotationValue instanceof TypelessParamTagValueNode) {
			return true;
		}

		if (TypeHintHelper::isTraversableType(
			TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $functionPointer, $typeHint->getTypeHintWithoutNullabilitySymbol()),
			$traversableTypeHints
		)) {
			return false;
		}

		$annotationType = $annotationValue->type;

		if (AnnotationTypeHelper::containsStaticOrThisType($annotationType)) {
			return false;
		}

		if (
			AnnotationTypeHelper::containsJustTwoTypes($annotationType)
			|| (
				$enableUnionTypeHint
				&& (
					$annotationType instanceof UnionTypeNode
					|| (
						$annotationType instanceof IdentifierTypeNode
						&& TypeHintHelper::isUnofficialUnionTypeHint($annotationType->name)
					)
				)
			)
			|| (
				$enableIntersectionTypeHint
				&& $annotationType instanceof IntersectionTypeNode
			)
		) {
			$annotationTypeHint = AnnotationTypeHelper::print($annotationType);
			return TypeHintHelper::typeHintEqualsAnnotation(
				$phpcsFile,
				$functionPointer,
				$typeHint->getTypeHint(),
				$annotationTypeHint
			);
		}

		if ($annotationType instanceof ObjectShapeNode) {
			return false;
		}

		if ($annotationType instanceof ConstTypeNode) {
			return false;
		}

		if ($annotationType instanceof GenericTypeNode) {
			return false;
		}

		if ($annotationType instanceof CallableTypeNode) {
			return false;
		}

		if ($annotationType instanceof ConditionalTypeNode) {
			return false;
		}

		if ($annotationType instanceof ConditionalTypeForParameterNode) {
			return false;
		}

		if ($annotationType instanceof IdentifierTypeNode) {
			if (in_array(
				strtolower($annotationType->name),
				['true', 'false', 'null'],
				true
			)) {
				return $enableStandaloneNullTrueFalseTypeHints;
			}

			if (in_array(
				strtolower($annotationType->name),
				['class-string', 'trait-string', 'callable-string', 'numeric-string', 'non-empty-string', 'non-falsy-string', 'literal-string', 'positive-int', 'negative-int'],
				true
			)) {
				return false;
			}
		}

		$annotationTypeHint = AnnotationTypeHelper::getTypeHintFromOneType($annotationType);
		return TypeHintHelper::typeHintEqualsAnnotation(
			$phpcsFile,
			$functionPointer,
			$typeHint->getTypeHintWithoutNullabilitySymbol(),
			$annotationTypeHint
		);
	}

	private static function getStartPointer(File $phpcsFile, int $docCommentOpenPointer, PhpDocTagNode $annotationNode): int
	{
		$tokens = $phpcsFile->getTokens();

		$tagStartLine = $tokens[$docCommentOpenPointer]['line'] + $annotationNode->getAttribute('startLine') - 1;

		$searchPointer = $docCommentOpenPointer + 1;
		for ($i = $docCommentOpenPointer + 1; $i < $tokens[$docCommentOpenPointer]['comment_closer']; $i++) {
			if ($tagStartLine === $tokens[$i]['line']) {
				$searchPointer = $i;
				break;
			}
		}

		return TokenHelper::findNext($phpcsFile, TokenHelper::$annotationTokenCodes, $searchPointer);
	}

	private static function getEndPointer(
		File $phpcsFile,
		ParsedDocComment $parsedDocComment,
		int $annotationStartPointer,
		PhpDocTagNode $annotationNode
	): int
	{
		$tokens = $phpcsFile->getTokens();

		$annotationContent = $parsedDocComment->getTokens()->getContentBetween(
			$annotationNode->getAttribute(Attribute::START_INDEX),
			$annotationNode->getAttribute(Attribute::END_INDEX) + 1
		);
		$annotationLength = strlen($annotationContent);

		$searchPointer = $annotationStartPointer;

		$content = '';
		for ($i = $annotationStartPointer; $i < count($tokens); $i++) {
			$content .= $tokens[$i]['content'];

			if (strlen($content) >= $annotationLength) {
				$searchPointer = $i;
				break;
			}
		}

		$nextAnnotationStartPointer = TokenHelper::findNext(
			$phpcsFile,
			TokenHelper::$annotationTokenCodes,
			$searchPointer + 1,
			$parsedDocComment->getClosePointer()
		);

		$pointerAfter = $nextAnnotationStartPointer ?? $parsedDocComment->getClosePointer();

		$stringPointerBefore = TokenHelper::findPrevious($phpcsFile, T_DOC_COMMENT_STRING, $pointerAfter - 1, $searchPointer);

		return $stringPointerBefore ?? $searchPointer;
	}

	private static function changeAnnotationNode(PhpDocTagNode $tagNode, Node $nodeToChange, Node $changedNode): PhpDocTagNode
	{
		static $visitor;
		static $traverser;

		if ($visitor === null) {
			$visitor = new class extends AbstractNodeVisitor {

				/** @var Node */
				private $nodeToChange;

				/** @var Node */
				private $changedNode;

				/**
				 * @return Node|list<Node>|NodeTraverser::*|null
				 */
				public function enterNode(Node $node)
				{
					if ($node->getAttribute(Attribute::ORIGINAL_NODE) === $this->nodeToChange) {
						return $this->changedNode;
					}

					return null;
				}

				public function setNodeToChange(Node $nodeToChange): void
				{
					$this->nodeToChange = $nodeToChange;
				}

				public function setChangedNode(Node $changedNode): void
				{
					$this->changedNode = $changedNode;
				}

			};
		}

		if ($traverser === null) {
			$traverser = new NodeTraverser([$visitor]);
		}

		$visitor->setNodeToChange($nodeToChange);
		$visitor->setChangedNode($changedNode);

		[$changedTagNode] = $traverser->traverse([$tagNode]);

		return $changedTagNode;
	}

}
