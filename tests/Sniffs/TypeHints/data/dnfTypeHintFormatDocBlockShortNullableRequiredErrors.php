<?php

class Whatever
{

	/**
	 * @param string|null $param
	 * @return int|null
	 */
	public function method($param)
	{
	}

	/**
	 * @var bool|null
	 */
	private $property;

	/**
	 * @phpstan-type T array{value: float|null, valueVAT: float|null, type: ?string}
	 */
	public $template;

}

/**
 * @template T of float|null
 */
function whatever(): void
{
}
