<?php

namespace FooNamespace;

abstract class FooClass
{

	/**
	 * @return int
	 */
	public function withUselessDocCommentHavingUselessReturn(): int
	{
	}

	/**
	 * @param int $foo
	 */
	public function withUselessDocCommentHavingUselessParameter(int $foo)
	{
	}

	/**
	 * @param int $foo
	 * @param string $bar
	 * @return int
	 */
	public function withUselessDocCommentHavingUselessParametersAndReturn(int $foo, string $bar): int
	{
	}

	/**
	 * @param int $foo
	 * @return int[]
	 */
	public function withUselessParameterButRequiredReturn(int $foo): array
	{
	}

	/**
	 * @param int $foo
	 * @param int[] $bar
	 * @param int $baz
	 * @return int
	 */
	public function withMultipleUselessParametersAndReturn(int $foo, array $bar, int $baz): int
	{
	}

	/**
	 * Test
	 * @param int $foo
	 */
	public function withDescriptionAndUselessParameter(int $foo)
	{
	}

	/**
	 * Test
	 * @return int
	 */
	public function withDescriptionAndUselessReturn(): int
	{
	}

	/**
	 * @useful test
	 * @param int $foo
	 */
	public function withUsefulAnnotationAndUselessParameter(int $foo)
	{
	}

	/**
	 * @useful test
	 * @return int
	 */
	public function withUsefulAnnotationAndUselessReturn(): int
	{
	}

	/**
	 * @param hello
	 * @param int $foo
	 * @param
	 */
	public function withMalformedParameters(int $foo)
	{
	}

	/**
	 * @param hello
	 * @useful hello
	 * @param int $foo
	 * @param
	 */
	public function withMalformedAndUsefulTags(int $foo)
	{
	}

}
