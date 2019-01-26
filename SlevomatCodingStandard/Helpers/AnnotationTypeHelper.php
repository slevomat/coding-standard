<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\ThisTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use function preg_replace;
use function substr;

/**
 * @internal
 */
class AnnotationTypeHelper
{

	/**
	 * @param \PHPStan\PhpDocParser\Ast\Type\TypeNode $typeNode
	 * @return \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode|\PHPStan\PhpDocParser\Ast\Type\ThisTypeNode
	 */
	public static function getTypeHintNode(TypeNode $typeNode): TypeNode
	{
		if ($typeNode instanceof ArrayTypeNode) {
			return self::getTypeHintNode($typeNode->type);
		}

		if ($typeNode instanceof NullableTypeNode) {
			return self::getTypeHintNode($typeNode->type);
		}

		if ($typeNode instanceof ThisTypeNode) {
			return $typeNode;
		}

		/** @var \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode $typeNode */
		$typeNode = $typeNode;
		return $typeNode;
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
		$exportedTypeNode = preg_replace('~\\s+~', '', (string) $typeNode);

		if ($typeNode instanceof UnionTypeNode) {
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

		if ($masterTypeNode instanceof ArrayTypeNode) {
			return new ArrayTypeNode(self::change($masterTypeNode->type, $typeNodeToChange, $changedTypeNode));
		}

		if ($masterTypeNode instanceof NullableTypeNode) {
			return new NullableTypeNode(self::change($masterTypeNode->type, $typeNodeToChange, $changedTypeNode));
		}

		return clone $masterTypeNode;
	}

}
