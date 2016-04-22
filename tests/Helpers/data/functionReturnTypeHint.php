<?php

abstract class FooClass
{

	public function withReturnTypeHint(): \FooNamespace\FooInterface
	{

	}

	final public function withoutReturnTypeHint()
	{

	}

	abstract public function abstractWithReturnTypeHint(): bool;

	abstract public function abstractWithoutReturnTypeHint();

}
