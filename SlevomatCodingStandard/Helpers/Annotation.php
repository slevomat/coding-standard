<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class Annotation
{

	/** @var string */
	private $name;

	/** @var string|null */
	private $parameters;

	/** @var string|null */
	private $content;

	public function __construct(
		string $name,
		?string $parameters,
		?string $content
	)
	{
		$this->name = $name;
		$this->parameters = $parameters;
		$this->content = $content;
	}

	public function getName(): string
	{
		return $this->name;
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
