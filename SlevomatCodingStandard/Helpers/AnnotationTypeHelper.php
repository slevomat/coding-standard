<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprFloatNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprIntegerNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprStringNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayShapeNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\CallableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\ConditionalTypeForParameterNode;
use PHPStan\PhpDocParser\Ast\Type\ConditionalTypeNode;
use PHPStan\PhpDocParser\Ast\Type\ConstTypeNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IntersectionTypeNode;
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\ObjectShapeNode;
use PHPStan\PhpDocParser\Ast\Type\ThisTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use function count;
use function in_array;
use function preg_match;
use function strtolower;

/**
 * @internal
 */
class AnnotationTypeHelper
{

	public static function print(TypeNode $typeNode): string
	{
		return PhpDocParserHelper::getPrinter()->print($typeNode);
	}

	public static function containsStaticOrThisType(TypeNode $typeNode): bool
	{
		if ($typeNode instanceof ThisTypeNode) {
			return true;
		}

		if ($typeNode instanceof IdentifierTypeNode) {
			return strtolower($typeNode->name) === 'static';
		}

		if (
			$typeNode instanceof UnionTypeNode
			|| $typeNode instanceof IntersectionTypeNode
		) {
			foreach ($typeNode->types as $innerTypeNode) {
				if (self::containsStaticOrThisType($innerTypeNode)) {
					return true;
				}
			}
		}

		return false;
	}

	public static function containsOneType(TypeNode $typeNode): bool
	{
		if ($typeNode instanceof IdentifierTypeNode) {
			return true;
		}

		if ($typeNode instanceof ThisTypeNode) {
			return true;
		}

		if ($typeNode instanceof GenericTypeNode) {
			return true;
		}

		if ($typeNode instanceof CallableTypeNode) {
			return true;
		}

		if ($typeNode instanceof ObjectShapeNode) {
			return true;
		}

		if ($typeNode instanceof ArrayShapeNode) {
			return true;
		}

		if ($typeNode instanceof ArrayTypeNode) {
			return true;
		}

		if ($typeNode instanceof ConstTypeNode) {
			if ($typeNode->constExpr instanceof ConstExprIntegerNode) {
				return true;
			}

			if ($typeNode->constExpr instanceof ConstExprFloatNode) {
				return true;
			}

			if ($typeNode->constExpr instanceof ConstExprStringNode) {
				return true;
			}
		}

		return false;
	}

	public static function containsJustTwoTypes(TypeNode $typeNode): bool
	{
		if ($typeNode instanceof NullableTypeNode && self::containsOneType($typeNode->type)) {
			return true;
		}

		if (
			!$typeNode instanceof UnionTypeNode
			&& !$typeNode instanceof IntersectionTypeNode
		) {
			return false;
		}

		return count($typeNode->types) === 2;
	}

	/**
	 * @param list<string> $traversableTypeHints
	 */
	public static function containsTraversableType(TypeNode $typeNode, File $phpcsFile, int $pointer, array $traversableTypeHints): bool
	{
		if ($typeNode instanceof GenericTypeNode) {
			return true;
		}

		if ($typeNode instanceof ObjectShapeNode) {
			return false;
		}

		if ($typeNode instanceof ArrayShapeNode) {
			return true;
		}

		if ($typeNode instanceof ArrayTypeNode) {
			return true;
		}

		if ($typeNode instanceof IdentifierTypeNode) {
			$fullyQualifiedType = TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $pointer, $typeNode->name);
			return TypeHintHelper::isTraversableType($fullyQualifiedType, $traversableTypeHints);
		}

		if (
			$typeNode instanceof UnionTypeNode
			|| $typeNode instanceof IntersectionTypeNode
		) {
			foreach ($typeNode->types as $innerTypeNode) {
				if (self::containsTraversableType($innerTypeNode, $phpcsFile, $pointer, $traversableTypeHints)) {
					return true;
				}
			}
		}

		return
			(
				$typeNode instanceof ConditionalTypeNode
				|| $typeNode instanceof ConditionalTypeForParameterNode
			) && (
				self::containsTraversableType($typeNode->if, $phpcsFile, $pointer, $traversableTypeHints)
				|| self::containsTraversableType($typeNode->else, $phpcsFile, $pointer, $traversableTypeHints)
			);
	}

	/**
	 * @param list<string> $traversableTypeHints
	 */
	public static function containsItemsSpecificationForTraversable(
		TypeNode $typeNode,
		File $phpcsFile,
		int $pointer,
		array $traversableTypeHints,
		bool $inTraversable = false
	): bool
	{
		if ($typeNode instanceof GenericTypeNode) {
			foreach ($typeNode->genericTypes as $genericType) {
				if (!self::containsItemsSpecificationForTraversable($genericType, $phpcsFile, $pointer, $traversableTypeHints, true)) {
					return false;
				}
			}

			return true;
		}

		if ($typeNode instanceof ArrayShapeNode || $typeNode instanceof ObjectShapeNode) {
			foreach ($typeNode->items as $innerItemNode) {
				if (!self::containsItemsSpecificationForTraversable(
					$innerItemNode->valueType,
					$phpcsFile,
					$pointer,
					$traversableTypeHints,
					true,
				)) {
					return false;
				}
			}

			return true;
		}

		if ($typeNode instanceof NullableTypeNode) {
			return self::containsItemsSpecificationForTraversable($typeNode->type, $phpcsFile, $pointer, $traversableTypeHints, true);
		}

		if ($typeNode instanceof IdentifierTypeNode) {
			if (TypeHintHelper::isTypeDefinedInAnnotation($phpcsFile, $pointer, $typeNode->name)) {
				// We can expect it's better type for traversable
				return true;
			}

			if (!$inTraversable) {
				return false;
			}

			return !TypeHintHelper::isTraversableType(
				TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $pointer, $typeNode->name),
				$traversableTypeHints,
			);
		}

		if ($typeNode instanceof ConstTypeNode) {
			return $inTraversable;
		}

		if ($typeNode instanceof CallableTypeNode) {
			return $inTraversable;
		}

		if ($typeNode instanceof ArrayTypeNode) {
			return self::containsItemsSpecificationForTraversable($typeNode->type, $phpcsFile, $pointer, $traversableTypeHints, true);
		}

		if (
			$typeNode instanceof UnionTypeNode
			|| $typeNode instanceof IntersectionTypeNode
		) {
			foreach ($typeNode->types as $innerTypeNode) {
				if (
					!$inTraversable
					&& $innerTypeNode instanceof IdentifierTypeNode
					&& strtolower($innerTypeNode->name) === 'null'
				) {
					continue;
				}

				if (self::containsItemsSpecificationForTraversable(
					$innerTypeNode,
					$phpcsFile,
					$pointer,
					$traversableTypeHints,
					$inTraversable,
				)) {
					return true;
				}
			}
		}

		if ($typeNode instanceof ConditionalTypeNode || $typeNode instanceof ConditionalTypeForParameterNode) {
			return
				self::containsItemsSpecificationForTraversable($typeNode->if, $phpcsFile, $pointer, $traversableTypeHints, $inTraversable)
				|| self::containsItemsSpecificationForTraversable(
					$typeNode->else,
					$phpcsFile,
					$pointer,
					$traversableTypeHints,
					$inTraversable,
				);
		}

		return false;
	}

	public static function getTypeHintFromOneType(
		TypeNode $typeNode,
		bool $enableUnionTypeHint = false,
		bool $enableStandaloneNullTrueFalseTypeHints = false
	): string
	{
		if ($typeNode instanceof GenericTypeNode) {
			$genericName = $typeNode->type->name;

			if (in_array(strtolower($genericName), ['non-empty-array', 'list', 'non-empty-list'], true)) {
				return 'array';
			}

			return $genericName;
		}

		if ($typeNode instanceof IdentifierTypeNode) {
			if (strtolower($typeNode->name) === 'true') {
				return $enableStandaloneNullTrueFalseTypeHints ? 'true' : 'bool';
			}

			if (strtolower($typeNode->name) === 'false') {
				return $enableUnionTypeHint || $enableStandaloneNullTrueFalseTypeHints ? 'false' : 'bool';
			}

			if (in_array(
				strtolower($typeNode->name),
				['positive-int', 'non-positive-int', 'negative-int', 'non-negative-int', 'literal-int', 'int-mask'],
				true,
			)) {
				return 'int';
			}

			if (in_array(
				strtolower($typeNode->name),
				['callable-array', 'callable-string'],
				true,
			)) {
				return 'callable';
			}

			// See https://psalm.dev/docs/annotating_code/type_syntax/scalar_types/#class-string-interface-string
			if (preg_match('~-string$~i', $typeNode->name) === 1) {
				return 'string';
			}

			if (in_array(
				strtolower($typeNode->name),
				['non-empty-array', 'list', 'non-empty-list'],
				true,
			)) {
				return 'array';
			}

			return $typeNode->name;
		}

		if ($typeNode instanceof CallableTypeNode) {
			return $typeNode->identifier->name;
		}

		if ($typeNode instanceof ArrayTypeNode) {
			return 'array';
		}

		if ($typeNode instanceof ArrayShapeNode) {
			return 'array';
		}

		if ($typeNode instanceof ObjectShapeNode) {
			return 'object';
		}

		if ($typeNode instanceof ConstTypeNode) {
			if ($typeNode->constExpr instanceof ConstExprIntegerNode) {
				return 'int';
			}

			if ($typeNode->constExpr instanceof ConstExprFloatNode) {
				return 'float';
			}

			if ($typeNode->constExpr instanceof ConstExprStringNode) {
				return 'string';
			}
		}

		return (string) $typeNode;
	}

	/**
	 * @param UnionTypeNode|IntersectionTypeNode $typeNode
	 * @param list<string> $traversableTypeHints
	 * @return list<string>
	 */
	public static function getTraversableTypeHintsFromType(
		TypeNode $typeNode,
		File $phpcsFile,
		int $pointer,
		array $traversableTypeHints,
		bool $enableUnionTypeHint = false
	): array
	{
		$typeHints = [];

		foreach ($typeNode->types as $type) {
			if (
				$type instanceof GenericTypeNode
				|| $type instanceof ThisTypeNode
				|| $type instanceof IdentifierTypeNode
			) {
				$typeHints[] = self::getTypeHintFromOneType($type);
			}
		}

		if (!$enableUnionTypeHint && count($typeHints) > 1) {
			return [];
		}

		foreach ($typeHints as $typeHint) {
			if (!TypeHintHelper::isTraversableType(
				TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $pointer, $typeHint),
				$traversableTypeHints,
			)) {
				return [];
			}
		}

		return $typeHints;
	}

	/**
	 * @param UnionTypeNode|IntersectionTypeNode $typeNode
	 */
	public static function getItemsSpecificationTypeFromType(TypeNode $typeNode): ?TypeNode
	{
		foreach ($typeNode->types as $type) {
			if ($type instanceof ArrayTypeNode) {
				return $type;
			}
		}

		return null;
	}

}
