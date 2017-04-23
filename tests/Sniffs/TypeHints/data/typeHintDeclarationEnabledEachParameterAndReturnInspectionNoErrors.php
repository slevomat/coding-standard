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
	public function withParameterSuppress(int $foo, array $bar)
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

}
