<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstFetchNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\InvalidTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\CallableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\ThisTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;
use SlevomatCodingStandard\Helpers\Annotation\Annotation;
use SlevomatCodingStandard\Helpers\Annotation\GenericAnnotation;
use SlevomatCodingStandard\Helpers\Annotation\MethodAnnotation;
use SlevomatCodingStandard\Helpers\Annotation\ParameterAnnotation;
use SlevomatCodingStandard\Helpers\Annotation\PropertyAnnotation;
use SlevomatCodingStandard\Helpers\Annotation\ReturnAnnotation;
use SlevomatCodingStandard\Helpers\Annotation\ThrowsAnnotation;
use SlevomatCodingStandard\Helpers\Annotation\VariableAnnotation;
use function array_key_exists;
use function array_merge;
use function get_class;
use function in_array;
use function preg_match;
use function preg_replace;
use function substr_count;
use function trim;
use const T_DOC_COMMENT_CLOSE_TAG;
use const T_DOC_COMMENT_STAR;
use const T_DOC_COMMENT_STRING;
use const T_DOC_COMMENT_TAG;
use const T_DOC_COMMENT_WHITESPACE;

class AnnotationHelper
{

	/**
	 * @internal
	 * @param VariableAnnotation|ParameterAnnotation|ReturnAnnotation|ThrowsAnnotation|PropertyAnnotation|MethodAnnotation $annotation
	 * @return TypeNode[]
	 */
	public static function getAnnotationTypes(Annotation $annotation): array
	{
		$annotationTypes = [];

		if ($annotation instanceof MethodAnnotation) {
			if ($annotation->getMethodReturnType() !== null) {
				$annotationTypes[] = $annotation->getMethodReturnType();
			}
			foreach ($annotation->getMethodParameters() as $methodParameterAnnotation) {
				if ($methodParameterAnnotation->type === null) {
					continue;
				}

				$annotationTypes[] = $methodParameterAnnotation->type;
			}
		} else {
			$annotationTypes[] = $annotation->getType();
		}

		return $annotationTypes;
	}

	/**
	 * @internal
	 * @param VariableAnnotation|ParameterAnnotation|ReturnAnnotation|ThrowsAnnotation|PropertyAnnotation|MethodAnnotation $annotation
	 * @return ConstExprNode[]
	 */
	public static function getAnnotationConstantExpressions(Annotation $annotation): array
	{
		if (!$annotation instanceof MethodAnnotation) {
			return [];
		}

		$constantExpressions = [];

		foreach ($annotation->getMethodParameters() as $methodParameterAnnotation) {
			if ($methodParameterAnnotation->defaultValue === null) {
				continue;
			}

			$constantExpressions[] = $methodParameterAnnotation->defaultValue;
		}

		return $constantExpressions;
	}

	/**
	 * @internal
	 * @param File $phpcsFile
	 * @param VariableAnnotation|ParameterAnnotation|ReturnAnnotation|ThrowsAnnotation|PropertyAnnotation|MethodAnnotation $annotation
	 * @param TypeNode $typeNode
	 * @param TypeNode $fixedTypeNode
	 * @return string
	 */
	public static function fixAnnotationType(File $phpcsFile, Annotation $annotation, TypeNode $typeNode, TypeNode $fixedTypeNode): string
	{
		if ($annotation instanceof MethodAnnotation) {
			$fixedContentNode = clone $annotation->getContentNode();

			if ($fixedContentNode->returnType !== null) {
				$fixedContentNode->returnType = AnnotationTypeHelper::change($fixedContentNode->returnType, $typeNode, $fixedTypeNode);
			}
			foreach ($fixedContentNode->parameters as $parameterNo => $parameterNode) {
				if ($parameterNode->type === null) {
					continue;
				}

				$fixedContentNode->parameters[$parameterNo] = clone $parameterNode;
				$fixedContentNode->parameters[$parameterNo]->type = AnnotationTypeHelper::change($parameterNode->type, $typeNode, $fixedTypeNode);
			}

			$fixedAnnotation = new MethodAnnotation(
				$annotation->getName(),
				$annotation->getStartPointer(),
				$annotation->getEndPointer(),
				$annotation->getContent(),
				$fixedContentNode
			);
		} else {
			$fixedContentNode = clone $annotation->getContentNode();
			$fixedContentNode->type = AnnotationTypeHelper::change($annotation->getType(), $typeNode, $fixedTypeNode);

			$annotationClassName = get_class($annotation);
			$fixedAnnotation = new $annotationClassName(
				$annotation->getName(),
				$annotation->getStartPointer(),
				$annotation->getEndPointer(),
				$annotation->getContent(),
				$fixedContentNode
			);
		}

		return self::fix($phpcsFile, $annotation, $fixedAnnotation);
	}

	/**
	 * @internal
	 * @param File $phpcsFile
	 * @param MethodAnnotation $annotation
	 * @param ConstFetchNode $node
	 * @param ConstFetchNode $fixedNode
	 * @return string
	 */
	public static function fixAnnotationConstantFetchNode(
		File $phpcsFile,
		MethodAnnotation $annotation,
		ConstFetchNode $node,
		ConstFetchNode $fixedNode
	): string
	{
		$fixedContentNode = clone $annotation->getContentNode();

		foreach ($fixedContentNode->parameters as $parameterNo => $parameterNode) {
			if ($parameterNode->defaultValue === null) {
				continue;
			}

			$fixedContentNode->parameters[$parameterNo] = clone $parameterNode;
			$fixedContentNode->parameters[$parameterNo]->defaultValue = AnnotationConstantExpressionHelper::change($parameterNode->defaultValue, $node, $fixedNode);
		}

		$fixedAnnotation = new MethodAnnotation(
			$annotation->getName(),
			$annotation->getStartPointer(),
			$annotation->getEndPointer(),
			$annotation->getContent(),
			$fixedContentNode
		);

		return self::fix($phpcsFile, $annotation, $fixedAnnotation);
	}

	/**
	 * @param File $phpcsFile
	 * @param int $pointer
	 * @param string $annotationName
	 * @return (VariableAnnotation|ParameterAnnotation|ReturnAnnotation|ThrowsAnnotation|PropertyAnnotation|MethodAnnotation|GenericAnnotation)[]
	 */
	public static function getAnnotationsByName(File $phpcsFile, int $pointer, string $annotationName): array
	{
		$annotations = self::getAnnotations($phpcsFile, $pointer);

		return $annotations[$annotationName] ?? [];
	}

	/**
	 * @param File $phpcsFile
	 * @param int $pointer
	 * @return (VariableAnnotation|ParameterAnnotation|ReturnAnnotation|ThrowsAnnotation|PropertyAnnotation|MethodAnnotation|GenericAnnotation)[][]
	 */
	public static function getAnnotations(File $phpcsFile, int $pointer): array
	{
		$annotations = [];

		$docCommentOpenToken = DocCommentHelper::findDocCommentOpenToken($phpcsFile, $pointer);
		if ($docCommentOpenToken === null) {
			return $annotations;
		}

		$annotationNameCodes = array_merge([T_DOC_COMMENT_TAG], Tokens::$phpcsCommentTokens);

		$tokens = $phpcsFile->getTokens();
		$i = $docCommentOpenToken + 1;
		while ($i < $tokens[$docCommentOpenToken]['comment_closer']) {
			if (!in_array($tokens[$i]['code'], $annotationNameCodes, true)) {
				$i++;
				continue;
			}

			$annotationStartPointer = $i;
			$annotationEndPointer = $i;

			// Fix for wrong PHPCS parsing
			$parenthesesLevel = substr_count($tokens[$i]['content'], '(') - substr_count($tokens[$i]['content'], ')');
			$annotationCode = $tokens[$i]['content'];

			for ($j = $i + 1; $j <= $tokens[$docCommentOpenToken]['comment_closer']; $j++) {
				if ($tokens[$j]['code'] === T_DOC_COMMENT_CLOSE_TAG) {
					$i = $j;
					break;
				}

				if (in_array($tokens[$j]['code'], $annotationNameCodes, true) && $parenthesesLevel === 0) {
					$i = $j;
					break;
				}

				if ($tokens[$j]['code'] === T_DOC_COMMENT_STAR) {
					continue;
				}

				if (in_array($tokens[$j]['code'], array_merge([T_DOC_COMMENT_STRING], $annotationNameCodes), true)) {
					$annotationEndPointer = $j;
				} elseif ($tokens[$j]['code'] === T_DOC_COMMENT_WHITESPACE) {
					if (array_key_exists($j - 1, $tokens) && $tokens[$j - 1]['code'] === T_DOC_COMMENT_STAR) {
						continue;
					}
					if (array_key_exists($j + 1, $tokens) && $tokens[$j + 1]['code'] === T_DOC_COMMENT_STAR) {
						continue;
					}
				}

				$parenthesesLevel += substr_count($tokens[$j]['content'], '(') - substr_count($tokens[$j]['content'], ')');
				$annotationCode .= $tokens[$j]['content'];
			}

			$annotationName = $tokens[$annotationStartPointer]['content'];
			$annotationParameters = null;
			$annotationContent = null;
			if (preg_match('~^(@[-a-zA-Z\\\\:]+)(?:\((.*)\))?(?:\\s+(.+))?($)~s', trim($annotationCode), $matches) !== 0) {
				$annotationName = $matches[1];
				$annotationParameters = trim($matches[2]);
				if ($annotationParameters === '') {
					$annotationParameters = null;
				}
				$annotationContent = trim($matches[3]);
				if ($annotationContent === '') {
					$annotationContent = null;
				}
			}

			$mapping = [
				'@param' => ParameterAnnotation::class,
				'@return' => ReturnAnnotation::class,
				'@var' => VariableAnnotation::class,
				'@throws' => ThrowsAnnotation::class,
				'@property' => PropertyAnnotation::class,
				'@property-read' => PropertyAnnotation::class,
				'@property-write' => PropertyAnnotation::class,
				'@method' => MethodAnnotation::class,
			];

			if (array_key_exists($annotationName, $mapping)) {
				$className = $mapping[$annotationName];

				$parsedContent = null;
				if ($annotationContent !== null) {
					$parsedContent = self::parseAnnotationContent($annotationName, $annotationContent);
					if ($parsedContent instanceof InvalidTagValueNode) {
						$parsedContent = null;
					}
				}

				$annotation = new $className($annotationName, $annotationStartPointer, $annotationEndPointer, $annotationContent, $parsedContent);
			} else {
				$annotation = new GenericAnnotation($annotationName, $annotationStartPointer, $annotationEndPointer, $annotationParameters, $annotationContent);
			}

			$annotations[$annotationName][] = $annotation;
		}

		return $annotations;
	}

	/**
	 * @param File $phpcsFile
	 * @param int $functionPointer
	 * @param ReturnTypeHint|ParameterTypeHint|PropertyTypeHint|null $typeHint
	 * @param ReturnAnnotation|ParameterAnnotation|VariableAnnotation $annotation
	 * @param array<int, string> $traversableTypeHints
	 * @return bool
	 */
	public static function isAnnotationUseless(
		File $phpcsFile,
		int $functionPointer,
		$typeHint,
		Annotation $annotation,
		array $traversableTypeHints
	): bool
	{
		if ($typeHint === null || $annotation->getContent() === null) {
			return false;
		}

		if ($annotation->hasDescription()) {
			return false;
		}

		if (TypeHintHelper::isTraversableType(TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $functionPointer, $typeHint->getTypeHint()), $traversableTypeHints)) {
			return false;
		}

		if (AnnotationTypeHelper::containsStaticOrThisType($annotation->getType())) {
			return false;
		}

		if (AnnotationTypeHelper::isCompoundOfNull($annotation->getType())) {
			/** @var UnionTypeNode $annotationTypeNode */
			$annotationTypeNode = $annotation->getType();

			$annotationTypeHintNode = AnnotationTypeHelper::getTypeFromNullableType($annotationTypeNode);
			$annotationTypeHint = $annotationTypeHintNode instanceof IdentifierTypeNode ? $annotationTypeHintNode->name : (string) $annotationTypeHintNode;
			return TypeHintHelper::typeHintEqualsAnnotation($phpcsFile, $functionPointer, $typeHint->getTypeHint(), $annotationTypeHint);
		}

		if (!AnnotationTypeHelper::containsOneType($annotation->getType())) {
			return false;
		}

		if ($annotation->getType() instanceof GenericTypeNode) {
			return false;
		}

		if ($annotation->getType() instanceof CallableTypeNode) {
			return false;
		}

		/** @var GenericTypeNode|CallableTypeNode|IdentifierTypeNode|ThisTypeNode $annotationTypeNode */
		$annotationTypeNode = $annotation->getType();
		$annotationTypeHint = AnnotationTypeHelper::getTypeHintFromOneType($annotationTypeNode);
		return TypeHintHelper::typeHintEqualsAnnotation($phpcsFile, $functionPointer, $typeHint->getTypeHint(), $annotationTypeHint);
	}

	private static function fix(File $phpcsFile, Annotation $annotation, Annotation $fixedAnnotation): string
	{
		$spaceAfterContent = '';
		if (preg_match('~(\\s+)$~', TokenHelper::getContent($phpcsFile, $annotation->getStartPointer(), $annotation->getEndPointer()), $matches) > 0) {
			$spaceAfterContent = $matches[1];
		}

		$fixedAnnotationContent = $fixedAnnotation->export() . $spaceAfterContent;

		return preg_replace('~(\r\n|\n|\r)~', '\1 * ', $fixedAnnotationContent);
	}


	private static function parseAnnotationContent(string $annotationName, string $annotationContent): PhpDocTagValueNode
	{
		$annotationContentWithoutNewLines = preg_replace('~[\r\n]~', ' ', $annotationContent);

		$tokens = new TokenIterator(self::getPhpDocLexer()->tokenize($annotationContentWithoutNewLines));
		return self::getPhpDocParser()->parseTagValue($tokens, $annotationName);
	}

	private static function getPhpDocLexer(): Lexer
	{
		static $phpDocLexer;

		if ($phpDocLexer === null) {
			$phpDocLexer = new Lexer();
		}

		return $phpDocLexer;
	}

	private static function getPhpDocParser(): PhpDocParser
	{
		static $phpDocParser;

		if ($phpDocParser === null) {
			$phpDocParser = new PhpDocParser(new TypeParser(), new ConstExprParser());
		}

		return $phpDocParser;
	}

}
