<?php // lint >= 8.0

abstract class FooClass
{

	public function withReturnNullableTypeHint(): ?\FooNamespace\FooInterface
	{

	}

	abstract public function abstractWithReturnNullableTypeHint(): ?bool;

	public function unionWithNull(): null|string
	{
	}

}
