<?php

namespace FooNamespace;

abstract class FooClass
{

	/**
	 * @return object|null
	 */
	abstract public function withNullableReturnTypeHint();

	/**
	 * @param null|object $a
	 */
	public function withNullableParameterTypeHint($a): void
	{

	}

}
