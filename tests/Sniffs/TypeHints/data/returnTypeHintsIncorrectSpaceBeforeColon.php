<?php

function foo(): Foo
{

}

function fooo()  : Foo
{

}

$callback = function(): Foo
{

};

$callback = function ()  : Foo
{

};

$callback = function () use ($test): Foo
{

};

$callback = function () use ($test)  : Foo
{

};

interface Foo
{

	public function doFoo($param): Foo;

	public function doBar($param): \Bar;

	public function doBaz($param): \Foo\Bar;

	public function dooFoo($param): Foo;

	public function dooBar($param): \Bar;

	public function dooBaz($param): \Foo\Bar;

	public function doFoo2($param)  : Foo;

	public function doBar2($param)  : \Bar;

	public function doBaz2($param)  : \Foo\Bar;

	public function dooFoo2($param)  : Foo;

	public function dooBar2($param)  : \Bar;

	public function dooBaz2($param)  : \Foo\Bar;

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

	public function dooFoo($param)  : Foo
	{

	}

	public function dooBar($param)  : \Bar
	{

	}

	public function dooBaz($param)  : \Foo\Bar
	{

	}

}
