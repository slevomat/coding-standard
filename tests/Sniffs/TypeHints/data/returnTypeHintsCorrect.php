<?php

function foo(): Foo
{

}

$callback = function () use ($x, $y): Foo {

};

interface Foo
{

	public function doFoo($param): Foo;

	public function doBar($param): \Bar;

	public function doBaz($param): \Foo\Bar;

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

}
