<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class Annotation
{

	/** @var string */
	private $name;

	/** @var int */
	private $pointer;

	/** @var string|null */
	private $parameters;

	/** @var string|null */
	private $content;

	public function __construct(
		string $name,
		int $pointer,
		?string $parameters,
		?string $content
	)
	{
		$this->name = $name;
		$this->pointer = $pointer;
		$this->parameters = $parameters;
		$this->content = $content;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getPointer(): int
	{
		return $this->pointer;
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
