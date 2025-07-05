<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

/**
 * @internal
 */
class Attribute
{

	private int $attributePointer;

	private string $name;

	private string $fullyQualifiedName;

	private int $startPointer;

	private int $endPointer;

	private ?string $content = null;

	public function __construct(
		int $attributePointer,
		string $name,
		string $fullyQualifiedName,
		int $startPointer,
		int $endPointer,
		?string $content = null
	)
	{
		$this->attributePointer = $attributePointer;
		$this->name = $name;
		$this->fullyQualifiedName = $fullyQualifiedName;
		$this->startPointer = $startPointer;
		$this->endPointer = $endPointer;
		$this->content = $content;
	}

	public function getAttributePointer(): int
	{
		return $this->attributePointer;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getFullyQualifiedName(): string
	{
		return $this->fullyQualifiedName;
	}

	public function getStartPointer(): int
	{
		return $this->startPointer;
	}

	public function getEndPointer(): int
	{
		return $this->endPointer;
	}

	public function getContent(): ?string
	{
		return $this->content;
	}

}
