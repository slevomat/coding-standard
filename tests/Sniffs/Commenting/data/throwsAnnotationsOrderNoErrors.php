<?php

use Bar;
use Foo;

class NoErrorsClass
{

	/**
	 * @throws \ArgumentCountError
	 * @throws \Exception
	 * @throws \InvalidArgumentException
	 */
	public function orderedThrows(): void
	{
	}

	/**
	 * @throws \Exception
	 */
	public function singleThrow(): void
	{
	}

	/**
	 * No throws annotation
	 */
	public function noThrows(): void
	{
	}

	/**
	 * @param string $a
	 * @throws \Aaa
	 * @throws \Bbb
	 * @throws \Ccc
	 * @return int
	 */
	public function mixedAnnotations(string $a): int
	{
		return 0;
	}

	/**
	 * @throws Bar\Exception
	 * @throws Foo\ArgumentCountError
	 * @throws InvalidArgumentException
	 */
	public function unqualifiedOrdered(): void
	{
	}

	/**
	 * @throws A\Bc
	 * @throws Abc
	 * @throws C\D\E
	 * @throws \Cd\E
	 * @throws D\Ef
	 */
	public function namespacedMixed(): void
	{
	}

	/**
	 * @throws Def
	 * @throws Xyz|Abc
	 */
	public function unionType(): void
	{
	}

	/**
	 * @throws
	 * @throws Abc&Ghi
	 * @throws Def
	 */
	public function invalidAndIntersection(): void
	{
	}

}
