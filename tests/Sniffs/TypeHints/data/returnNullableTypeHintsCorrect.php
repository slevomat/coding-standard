<?php // lint >= 7.4

function foo(): Foo
{

}

$callback = function () use ($x, $y): ?Foo {

};

$callback = fn ($a): ?Foo => $a;

interface Foo
{

	public function doFoo($param): ?Foo;

	public function doBar($param): ?\Bar;

	public function doBaz($param): ?\Foo\Bar;

}

class FooBar implements Foo
{

	public function doFoo($param): ?Foo
	{

	}

	public function doBar($param): ?\Bar
	{

	}

	public function doBaz($param): ?\Foo\Bar
	{

	}

}
