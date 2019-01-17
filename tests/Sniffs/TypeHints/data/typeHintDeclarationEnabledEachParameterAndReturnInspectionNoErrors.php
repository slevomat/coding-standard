<?php

namespace FooNamespace;

abstract class FooClass
{

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration
	 * @param int $foo
	 * @return int
	 */
	public function withGlobalSuppress(int $foo, array $bar): int
	{
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.UselessParameterAnnotation
	 * @param int $foo
	 * @param int[] $bar
	 */
	public function withParameterSuppress(int $foo, array $bar): void
	{
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.UselessReturnAnnotation
	 * @param int[] $bar
	 * @return int
	 */
	public function withReturnSuppress(int $foo, array $bar): int
	{
	}

	/**
	 * @param int[] $bar
	 */
	public function withSomeParametersOmitted(int $foo, array $bar): int
	{
	}

	/**
	 * @param string $param1 Description
	 */
	public function withDescription(string $param1, int $param2): void
	{
	}

	/**
	 * @param string|null $param2 Has description
	 * @param string|null $param3 Has description
	 */
	public function moreParametersWithDescription(string $param1 = '', string $param2 = null, string $param3 = null): void
	{
	}

}
