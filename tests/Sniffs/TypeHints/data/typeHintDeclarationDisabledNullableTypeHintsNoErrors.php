<?php

namespace FooNamespace;

abstract class FooClass
{

	/**
	 * @return string|null
	 */
	abstract public function withNullableReturnTypeHint();

	public function withParameterTypeHint(string $a = null)
	{

	}

}
