<?php

class Whatever
{

	/**
	 * @param (Foo&Bar)|null $param
	 * @return (Baz&Qux) | null
	 */
	public function method($param)
	{
	}

	/**
	 * @var (Foo&Bar) | null
	 */
	private $property;

	/**
	 * @property (Foo&Bar) | null $propertyAnnotation
	 */
}

class WithoutIntersection
{

	/**
	 * @param string|null $noIntersection
	 */
	public function method($noIntersection)
	{
	}

}
