<?php

class Whatever
{

	/**
	 * @param null | string $param
	 * @return int | null
	 */
	public function method($param)
	{
	}

	/**
	 * @var bool | null
	 */
	private $property;

}

class WithIntersection
{

	/**
	 * @var (Foo & Bar) | null
	 */
	private $dnf;

}
