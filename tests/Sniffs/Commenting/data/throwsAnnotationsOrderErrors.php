<?php

use Bar;
use Foo;

class ErrorsClass
{

	/**
	 * @throws \InvalidArgumentException
	 * @throws \ArgumentCountError
	 * @throws \Exception
	 */
	public function unorderedThrows(): void
	{
	}

	/**
	 * @param string $a
	 * @throws \Ccc
	 * @throws \Aaa
	 * @throws \Bbb
	 * @return int
	 */
	public function mixedAnnotations(string $a): int
	{
		return 0;
	}

	/**
	 * @throws Foo\ArgumentCountError
	 * @throws InvalidArgumentException
	 * @throws Bar\Exception
 	 */
	public function unqualifiedUnordered(): void
	{
	}

	/**
	 * @throws \Zzz Description
	 * @throws \Aaa Another description
	 */
	public function withDescriptions(): void
	{
	}

	/**
	 * @throws Abc
	 * @throws A\Bc
	 * @throws \Cd\E
	 * @throws C\D\E
	 * @throws D\Ef
	 */
	public function namespacedMixed(): void
	{
	}

	/**
	 * @throws Xyz|Abc
	 * @throws Def
	 */
	public function unionType(): void
	{
	}

	/**
	 * @throws Def
	 * @throws Abc&Ghi
	 * @throws
	 */
	public function invalidAndIntersection(): void
	{
	}

}
