<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\CallableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\CallableTypeParameterNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IntersectionTypeNode;
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\ThisTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use function array_merge;
use function preg_replace;
use function substr;

/**
 * @internal
 */
class AnnotationTypeHelper
{

	/**
	 * @param \PHPStan\PhpDocParser\Ast\Type\TypeNode $typeNode
	 * @return \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode[]|\PHPStan\PhpDocParser\Ast\Type\ThisTypeNode[]
	 */
	public static function getIdentifierTypeNodes(TypeNode $typeNode): array
	{
		if ($typeNode instanceof ArrayTypeNode) {
			return self::getIdentifierTypeNodes($typeNode->type);
		}

		if (
			$typeNode instanceof UnionTypeNode
			|| $typeNode instanceof IntersectionTypeNode
		) {
			$identifierTypeNodes = [];
			foreach ($typeNode->types as $innerTypeNode) {
				$identifierTypeNodes = array_merge($identifierTypeNodes, self::getIdentifierTypeNodes($innerTypeNode));
			}
			return $identifierTypeNodes;
		}

		if ($typeNode instanceof GenericTypeNode) {
			$identifierTypeNodes = self::getIdentifierTypeNodes($typeNode->type);
			foreach ($typeNode->genericTypes as $innerTypeNode) {
				$identifierTypeNodes = array_merge($identifierTypeNodes, self::getIdentifierTypeNodes($innerTypeNode));
			}
			return $identifierTypeNodes;
		}

		if ($typeNode instanceof NullableTypeNode) {
			return self::getIdentifierTypeNodes($typeNode->type);
		}

		if ($typeNode instanceof CallableTypeNode) {
			$identifierTypeNodes = array_merge([$typeNode->identifier], self::getIdentifierTypeNodes($typeNode->returnType));
			foreach ($typeNode->parameters as $callableParameterNode) {
				$identifierTypeNodes = array_merge($identifierTypeNodes, self::getIdentifierTypeNodes($callableParameterNode->type));
			}
			return $identifierTypeNodes;
		}

		/** @var \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode|\PHPStan\PhpDocParser\Ast\Type\ThisTypeNode $typeNode */
		$typeNode = $typeNode;
		return [$typeNode];
	}

	/**
	 * @param \PHPStan\PhpDocParser\Ast\Type\TypeNode $typeNode
	 * @return \PHPStan\PhpDocParser\Ast\Type\UnionTypeNode[]
	 */
	public static function getUnionTypeNodes(TypeNode $typeNode): array
	{
		if ($typeNode instanceof UnionTypeNode) {
			return [$typeNode];
		}

		if ($typeNode instanceof NullableTypeNode) {
			return self::getUnionTypeNodes($typeNode->type);
		}

		if ($typeNode instanceof ArrayTypeNode) {
			return self::getUnionTypeNodes($typeNode->type);
		}

		if ($typeNode instanceof IntersectionTypeNode) {
			$unionTypeNodes = [];
			foreach ($typeNode->types as $innerTypeNode) {
				$unionTypeNodes = array_merge($unionTypeNodes, self::getUnionTypeNodes($innerTypeNode));
			}
			return $unionTypeNodes;
		}

		if ($typeNode instanceof GenericTypeNode) {
			$unionTypeNodes = [];
			foreach ($typeNode->genericTypes as $innerTypeNode) {
				$unionTypeNodes = array_merge($unionTypeNodes, self::getUnionTypeNodes($innerTypeNode));
			}
			return $unionTypeNodes;
		}

		if ($typeNode instanceof CallableTypeNode) {
			$unionTypeNodes = self::getUnionTypeNodes($typeNode->returnType);
			foreach ($typeNode->parameters as $callableParameterNode) {
				$unionTypeNodes = array_merge($unionTypeNodes, self::getUnionTypeNodes($callableParameterNode->type));
			}
			return $unionTypeNodes;
		}

		return [];
	}

	/**
	 * @param \PHPStan\PhpDocParser\Ast\Type\TypeNode $typeNode
	 * @return \PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode[]
	 */
	public static function getArrayTypeNodes(TypeNode $typeNode): array
	{
		if ($typeNode instanceof ArrayTypeNode) {
			return array_merge([$typeNode], self::getArrayTypeNodes($typeNode->type));
		}

		if ($typeNode instanceof NullableTypeNode) {
			return self::getArrayTypeNodes($typeNode->type);
		}

		if (
			$typeNode instanceof UnionTypeNode
			|| $typeNode instanceof IntersectionTypeNode
		) {
			$arrayTypeNodes = [];
			foreach ($typeNode->types as $innerTypeNode) {
				$arrayTypeNodes = array_merge($arrayTypeNodes, self::getArrayTypeNodes($innerTypeNode));
			}
			return $arrayTypeNodes;
		}

		if ($typeNode instanceof GenericTypeNode) {
			$arrayTypeNodes = [];
			foreach ($typeNode->genericTypes as $innerTypeNode) {
				$arrayTypeNodes = array_merge($arrayTypeNodes, self::getArrayTypeNodes($innerTypeNode));
			}
			return $arrayTypeNodes;
		}

		if ($typeNode instanceof CallableTypeNode) {
			$arrayTypeNodes = self::getArrayTypeNodes($typeNode->returnType);
			foreach ($typeNode->parameters as $callableParameterNode) {
				$arrayTypeNodes = array_merge($arrayTypeNodes, self::getArrayTypeNodes($callableParameterNode->type));
			}
			return $arrayTypeNodes;
		}

		return [];
	}

	/**
	 * @param \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode|\PHPStan\PhpDocParser\Ast\Type\ThisTypeNode $typeNode
	 * @return string
	 */
	public static function getTypeHintFromNode(TypeNode $typeNode): string
	{
		return $typeNode instanceof ThisTypeNode
			? (string) $typeNode
			: $typeNode->name;
	}

	public static function export(TypeNode $typeNode): string
	{
		$exportedTypeNode = preg_replace(['~\\s*(&|\|)\\s*~'], '\\1', (string) $typeNode);

		if (
			$typeNode instanceof UnionTypeNode
			|| $typeNode instanceof IntersectionTypeNode
		) {
			$exportedTypeNode = substr($exportedTypeNode, 1, -1);
		}

		return $exportedTypeNode;
	}

	public static function change(TypeNode $masterTypeNode, TypeNode $typeNodeToChange, TypeNode $changedTypeNode): TypeNode
	{
		if ($masterTypeNode === $typeNodeToChange) {
			return $changedTypeNode;
		}

		if ($masterTypeNode instanceof UnionTypeNode) {
			$types = [];
			foreach ($masterTypeNode->types as $typeNone) {
				$types[] = self::change($typeNone, $typeNodeToChange, $changedTypeNode);
			}

			return new UnionTypeNode($types);
		}

		if ($masterTypeNode instanceof IntersectionTypeNode) {
			$types = [];
			foreach ($masterTypeNode->types as $typeNone) {
				$types[] = self::change($typeNone, $typeNodeToChange, $changedTypeNode);
			}

			return new IntersectionTypeNode($types);
		}

		if ($masterTypeNode instanceof GenericTypeNode) {
			$genericTypes = [];
			foreach ($masterTypeNode->genericTypes as $genericTypeNode) {
				$genericTypes[] = self::change($genericTypeNode, $typeNodeToChange, $changedTypeNode);
			}

			/** @var \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode $identificatorTypeNode */
			$identificatorTypeNode = self::change($masterTypeNode->type, $typeNodeToChange, $changedTypeNode);
			return new GenericTypeNode($identificatorTypeNode, $genericTypes);
		}

		if ($masterTypeNode instanceof ArrayTypeNode) {
			return new ArrayTypeNode(self::change($masterTypeNode->type, $typeNodeToChange, $changedTypeNode));
		}

		if ($masterTypeNode instanceof NullableTypeNode) {
			return new NullableTypeNode(self::change($masterTypeNode->type, $typeNodeToChange, $changedTypeNode));
		}

		if ($masterTypeNode instanceof CallableTypeNode) {
			$callableParameters = [];
			foreach ($masterTypeNode->parameters as $parameterTypeNode) {
				$callableParameters[] = new CallableTypeParameterNode(
					self::change($parameterTypeNode->type, $typeNodeToChange, $changedTypeNode),
					$parameterTypeNode->isReference,
					$parameterTypeNode->isVariadic,
					$parameterTypeNode->parameterName,
					$parameterTypeNode->isOptional
				);
			}

			/** @var \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode $identificatorTypeNode */
			$identificatorTypeNode = self::change($masterTypeNode->identifier, $typeNodeToChange, $changedTypeNode);
			return new CallableTypeNode(
				$identificatorTypeNode,
				$callableParameters,
				self::change($masterTypeNode->returnType, $typeNodeToChange, $changedTypeNode)
			);
		}

		return clone $masterTypeNode;
	}

}
