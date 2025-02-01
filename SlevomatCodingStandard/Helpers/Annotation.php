<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHPStan\PhpDocParser\Ast\PhpDoc\InvalidTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagValueNode;

/**
 * @internal
 * @template T of PhpDocTagValueNode
 */
class Annotation
{

	private PhpDocTagNode $node;

	private int $startPointer;

	private int $endPointer;

	public function __construct(PhpDocTagNode $node, int $startPointer, int $endPointer)
	{
		$this->node = $node;
		$this->startPointer = $startPointer;
		$this->endPointer = $endPointer;
	}

	public function getNode(): PhpDocTagNode
	{
		return $this->node;
	}

	public function getName(): string
	{
		return $this->node->name;
	}

	/**
	 * @return T
	 */
	public function getValue(): PhpDocTagValueNode
	{
		/** @phpstan-ignore-next-line */
		return $this->node->value;
	}

	public function getStartPointer(): int
	{
		return $this->startPointer;
	}

	public function getEndPointer(): int
	{
		return $this->endPointer;
	}

	public function isInvalid(): bool
	{
		return $this->node->value instanceof InvalidTagValueNode;
	}

}
