<?php

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

}
