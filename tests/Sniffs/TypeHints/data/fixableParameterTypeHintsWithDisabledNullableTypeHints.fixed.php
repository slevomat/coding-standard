<?php // lint >= 7.1

namespace FooNamespace;

use DateTimeImmutable;

/**
 * @param int|null $a
 */
function nullableSimpleTypeHint(int $a = null)
{
}

/**
 * @param null|int $a
 */
function nullableFirstSimpleTypeHint(int $a = null)
{
}

/**
 * @param \Foo|null $a
 */
function nullableFullyQualifiedClassTypeHint(\Foo $a = null)
{
}

/**
 * @param DateTimeImmutable|null $a
 */
function nullableUsedClassTypeHint(DateTimeImmutable $a = null)
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
function withoutTypeHintAndWithAnnotationWithoutParameterName(string $a, bool $b = null)
{
}

/**
 * @param string $b
 * @param bool|null
 * @param float $c
 */
function oneWithoutTypeHintAndWithAnnotationWithoutParameterName(string $a, bool $b = null, float $c)
{
}

/**
 * @param string|null $a
 */
function optionalWithoutTypeHint(string $a = null)
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
	private function nullableSimpleTypeHint(int $a = null)
	{
	}

	/**
	 * @param null|int $a
	 */
	private function nullableFirstSimpleTypeHint(int $a = null)
	{
	}

	/**
	 * @param \Foo|null $a
	 */
	private function nullableFullyQualifiedClassTypeHint(\Foo $a = null)
	{
	}

	/**
	 * @param DateTimeImmutable|null $a
	 */
	private function nullableUsedClassTypeHint(DateTimeImmutable $a = null)
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
	public function withoutTypeHintAndWithAnnotationWithoutParameterName(string $a, bool $b = null)
	{
	}

	/**
	 * @param string $b
	 * @param bool|null
	 * @param float $c
	 */
	public function oneWithoutTypeHintAndWithAnnotationWithoutParameterName(string $a, bool $b = null, float $c)
	{
	}

	/**
	 * @param string|null $a
	 */
	public function optionalWithoutTypeHint(string $a = null)
	{

	}

	/**
	 * @param $a string
	 */
	private function invalidAnnotation($a)
	{

	}

}
