<?php

use Bar;
use Foo;

class ErrorsClass
{

	/**
	 * @throws \ArgumentCountError
	 * @throws \Exception
	 * @throws \InvalidArgumentException
	 */
	public function unorderedThrows(): void
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
	public function unqualifiedUnordered(): void
	{
	}

	/**
	 * @throws \Aaa Another description
	 * @throws \Zzz Description
	 */
	public function withDescriptions(): void
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
