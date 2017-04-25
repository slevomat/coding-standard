<?php

namespace FooNamespace;

abstract class FooClass
{

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param int $foo
	 */
	abstract public function withAbstractUntypedParameter($foo);

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param int $foo
	 */
	public function withUntypedParameter($foo)
	{

	}

}

interface BarInterface
{

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param int $foo
	 */
	public function withInterfaceUntypedParameter($foo);

}

final class BazClass extends FooClass implements BarInterface
{

	/**
	 * @param int $foo
	 */
	public function withInterfaceUntypedParameter($foo)
	{

	}

	/**
	 * @param int $foo
	 */
	public function withAbstractUntypedParameter($foo)
	{

	}

	/**
	 * @param int $foo
	 */
	public function withUntypedParameter($foo)
	{

	}

}
