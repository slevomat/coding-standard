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

}
