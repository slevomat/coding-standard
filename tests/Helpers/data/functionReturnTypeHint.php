<?php // lint >= 8.0

abstract class FooClass
{

	public function withReturnTypeHint(): \FooNamespace\FooInterface
	{

	}

	public function withReturnTypeHintNoSpace(): \FooNamespace\FooInterface{

	}

	final public function withoutReturnTypeHint()
	{

	}

	abstract public function abstractWithReturnTypeHint(): bool;

	abstract public function abstractWithoutReturnTypeHint();

	public function unionReturnTypeHint(): string|int
	{
	}

	public function unionReturnTypeHintWithWhitespace(): string | int | bool
	{
	}

}
