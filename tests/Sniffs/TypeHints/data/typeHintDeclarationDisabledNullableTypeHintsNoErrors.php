<?php

namespace FooNamespace;

abstract class FooClass
{

	/**
	 * @return string|null
	 */
	abstract public function withNullableReturnTypeHint();

	public function withParameterTypeHint(string $a = null): void
	{

	}

	/**
	 * @param static|null $a
	 */
	public function nullableStaticAsSelf(self $a = null): void
	{

	}

}
