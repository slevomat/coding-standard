<?php

class Whatever
{

	/**
	 * @param ?string $param
	 * @return ?int
	 */
	public function method($param)
	{
	}

	/**
	 * @var ?bool
	 */
	private $property;

	/**
	 * @phpstan-type T array{value: ?float, valueVAT: ?float, type: ?string}
	 */
	public $template;

}

/**
 * @template T of ?float
 */
function whatever(): void
{
}
