<?php // lint >= 7.1

namespace FooNamespace;

use DateTimeImmutable;

/**
 * @param int|null $a
 */
function nullableSimpleTypeHint($a)
{
}

/**
 * @param null|int $a
 */
function nullableFirstSimpleTypeHint($a)
{
}

/**
 * @param \Foo|null $a
 */
function nullableFullyQualifiedClassTypeHint($a)
{
}

/**
 * @param DateTimeImmutable|null $a
 */
function nullableUsedClassTypeHint($a)
{
}

/**
 * @param resource|null $a
 */
function nullableUnofficialTypeHint($a)
{
}

/**
 * @param string Description
 * @param bool|null
 */
function withoutTypeHintAndWithAnnotationWithoutParameterName($a, $b)
{
}

/**
 * @param string $b
 * @param bool|null
 * @param float $c
 */
function oneWithoutTypeHintAndWithAnnotationWithoutParameterName(string $a, $b, float $c)
{
}

/**
 * @param string|null $a
 */
function optionalWithoutTypeHint($a = null)
{

}

/**
 * @param $a string
 */
function invalidAnnotation($a)
{

}

class Foo
{

	/**
	 * @param int|null $a
	 */
	private function nullableSimpleTypeHint($a)
	{
	}

	/**
	 * @param null|int $a
	 */
	private function nullableFirstSimpleTypeHint($a)
	{
	}

	/**
	 * @param \Foo|null $a
	 */
	private function nullableFullyQualifiedClassTypeHint($a)
	{
	}

	/**
	 * @param DateTimeImmutable|null $a
	 */
	private function nullableUsedClassTypeHint($a)
	{
	}

	/**
	 * @param resource|null $a
	 */
	private function nullableUnofficialTypeHint($a)
	{
	}

	/**
	 * @param string Description
	 * @param bool|null
	 */
	public function withoutTypeHintAndWithAnnotationWithoutParameterName($a, $b)
	{
	}

	/**
	 * @param string $b
	 * @param bool|null
	 * @param float $c
	 */
	public function oneWithoutTypeHintAndWithAnnotationWithoutParameterName(string $a, $b, float $c)
	{
	}

	/**
	 * @param string|null $a
	 */
	public function optionalWithoutTypeHint($a = null)
	{

	}

	/**
	 * @param $a string
	 */
	private function invalidAnnotation($a)
	{

	}

}
