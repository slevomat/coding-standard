<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

/**
 * @internal
 */
class Attribute
{

	/** @var int */
	private $attributePointer;

	/** @var string */
	private $name;

	/** @var int */
	private $startPointer;

	/** @var int */
	private $endPointer;

	/** @var string|null */
	private $content;

	public function __construct(int $attributePointer, string $name, int $startPointer, int $endPointer, ?string $content = null)
	{
		$this->attributePointer = $attributePointer;
		$this->name = $name;
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
