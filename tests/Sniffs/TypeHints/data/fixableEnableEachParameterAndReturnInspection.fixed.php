<?php

namespace FooNamespace;

abstract class FooClass
{

	public function withUselessDocCommentHavingUselessReturn(): int
	{
	}

	public function withUselessDocCommentHavingUselessParameter(int $foo)
	{
	}

	public function withUselessDocCommentHavingUselessParametersAndReturn(int $foo, string $bar): int
	{
	}

	/**
	 * @return int[]
	 */
	public function withUselessParameterButRequiredReturn(int $foo): array
	{
	}

	/**
	 * @param int[] $bar
	 */
	public function withMultipleUselessParametersAndReturn(int $foo, array $bar, int $baz): int
	{
	}

	/**
	 * Test
	 */
	public function withDescriptionAndUselessParameter(int $foo)
	{
	}

	/**
	 * Test
	 */
	public function withDescriptionAndUselessReturn(): int
	{
	}

	/**
	 * @useful test
	 */
	public function withUsefulAnnotationAndUselessParameter(int $foo)
	{
	}

	/**
	 * @useful test
	 */
	public function withUsefulAnnotationAndUselessReturn(): int
	{
	}

	public function withMalformedParameters(int $foo)
	{
	}

	/**
	 * @param
	 * @param hello
	 * @useful hello
	 */
	public function withMalformedAndUsefulTags(int $foo)
	{
	}

}
