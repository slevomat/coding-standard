<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprArrayItemNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprArrayNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstFetchNode;
use function array_merge;

/**
 * @internal
 */
class AnnotationConstantExpressionHelper
{

	/**
	 * @return array<int, ConstFetchNode>
	 */
	public static function getConstantFetchNodes(ConstExprNode $constantExpressionNode): array
	{
		if ($constantExpressionNode instanceof ConstExprArrayNode) {
			$constantFetchNodes = [];
			foreach ($constantExpressionNode->items as $itemConstantExpressionNode) {
				$constantFetchNodes = array_merge($constantFetchNodes, self::getConstantFetchNodes($itemConstantExpressionNode));
			}
			return $constantFetchNodes;
		}

		if ($constantExpressionNode instanceof ConstExprArrayItemNode) {
			$constantFetchNodes = self::getConstantFetchNodes($constantExpressionNode->value);
			if ($constantExpressionNode->key !== null) {
				$constantFetchNodes = array_merge($constantFetchNodes, self::getConstantFetchNodes($constantExpressionNode->key));
			}
			return $constantFetchNodes;
		}

		if ($constantExpressionNode instanceof ConstFetchNode) {
			return [$constantExpressionNode];
		}

		return [];
	}

	public static function change(ConstExprNode $masterNode, ConstExprNode $nodeToChange, ConstExprNode $changedNode): ConstExprNode
	{
		if ($masterNode === $nodeToChange) {
			return $changedNode;
		}

		if ($masterNode instanceof ConstExprArrayNode) {
			$items = [];
			foreach ($masterNode->items as $itemNode) {
				/** @var ConstExprArrayItemNode $changedItemNode */
				$changedItemNode = self::change($itemNode, $nodeToChange, $changedNode);

				$items[] = $changedItemNode;
			}

			return new ConstExprArrayNode($items);
		}

		if ($masterNode instanceof ConstExprArrayItemNode) {
			$key = $masterNode->key !== null ? self::change($masterNode->key, $nodeToChange, $changedNode) : null;
			$value = self::change($masterNode->value, $nodeToChange, $changedNode);

			return new ConstExprArrayItemNode($key, $value);
		}

		return clone $masterNode;
	}

}
