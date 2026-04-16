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

	/**
	 * @property int | null $propertyAnnotation
	 * @property-read string | null $propertyRead
	 */
}

class WithIntersection
{

	/**
	 * @var (Foo & Bar) | null
	 */
	private $dnf;

	/**
	 * @return (Foo & Bar) | (Baz & Qux) | null
	 */
	public function method()
	{
	}

}

class WithGeneric
{

	/**
	 * @var Foo<int | null>
	 */
	private $generic;

}
