<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

/**
 * @internal
 */
class Annotation
{

	/** @var string */
	private $name;

	/** @var int */
	private $startPointer;

	/** @var int */
	private $endPointer;

	/** @var string|null */
	private $parameters;

	/** @var string|null */
	private $content;

	public function __construct(
		string $name,
		int $startPointer,
		int $endPointer,
		?string $parameters,
		?string $content
	)
	{
		$this->name = $name;
		$this->startPointer = $startPointer;
		$this->endPointer = $endPointer;
		$this->parameters = $parameters;
		$this->content = $content;
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

	public function getParameters(): ?string
	{
		return $this->parameters;
	}

	public function getContent(): ?string
	{
		return $this->content;
	}

}
