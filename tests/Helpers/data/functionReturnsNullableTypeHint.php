<?php // lint >= 7.1

abstract class FooClass
{

	public function withReturnNullableTypeHint(): ?\FooNamespace\FooInterface
	{

	}

	abstract public function abstractWithReturnNullableTypeHint(): ?bool;

}
