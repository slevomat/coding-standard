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
	public function withUselessDocCommentHavingUselessParameter(int $foo): void
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
	public function withDescriptionAndUselessParameter(int $foo): void
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
	public function withUsefulAnnotationAndUselessParameter(int $foo): void
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
	 * @param
	 * @param string $foo
	 * @return mixed
	 */
	public function withInvalidParamAnnotation(string $foo)
	{
		return null;
	}

}
