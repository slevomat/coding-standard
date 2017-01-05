<?php

namespace FooNamespace;

abstract class FooClass
{

	/**
	 * @return string
	 */
	public function withReturnTypeHint()
	{
		return 'string';
	}

	/**
	 * @param string|null $a
	 */
	public function withNullableParameterTypeHint($a)
	{

	}

	/**
	 * @param string|null $a
	 */
	public function withNullableParameterTypeHintAndUselessDocComment(string $a = null)
	{

	}

}
