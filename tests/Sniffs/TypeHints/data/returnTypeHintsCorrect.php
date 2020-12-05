<?php // lint >= 8.0

function foo(): Foo
{

}

$callback = function () use ($x, $y): Foo {

};

function ()
{

};

fn ($a) => $a;

fn ($a): int => $a;

interface Foo
{

	public function doFoo($param): Foo;

	public function doBar($param): \Bar;

	public function doBaz($param): \Foo\Bar;

	public function noReturnTypeHint();

}

class FooBar implements Foo
{

	public function doFoo($param): Foo
	{

	}

	public function doBar($param): \Bar
	{

	}

	public function doBaz($param): \Foo\Bar
	{

	}

	public function noReturnTypeHint()
	{

	}

	public function unionReturnTypeHint(): int|bool
	{

	}

}
