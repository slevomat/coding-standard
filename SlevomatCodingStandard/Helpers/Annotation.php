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
		string $parameters = null,
		string $content = null
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

	/**
	 * @return string|null
	 */
	public function getParameters()
	{
		return $this->parameters;
	}

	/**
	 * @return string|null
	 */
	public function getContent()
	{
		return $this->content;
	}

}
