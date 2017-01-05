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

}
