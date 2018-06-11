<?php

function foo(): Foo
{

}

$callback = function () use ($x, $y): Foo {

};

function ()
{

};

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

}
