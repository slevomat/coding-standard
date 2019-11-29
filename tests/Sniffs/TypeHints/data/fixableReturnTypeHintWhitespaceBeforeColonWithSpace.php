<?php // lint >= 7.4

function foo(): Foo
{

}

function bar() : Bar
{

}

function baz()  : Baz
{

}

$callback = function(): Foo
{

};

$callback = function() : Foo
{

};

$callback = function()  : Foo
{

};

$callback = fn ($a): Foo => $a;

$callback = fn ($a) : Foo => $a;

$callback = fn ($a)  : Foo => $a;

interface Foo
{

	public function doFoo($param): Foo;

	public function dooFoo($param) : Foo;

	public function doooFoo($param)  : Foo;

}

class FooBar implements Foo
{

	public function doFoo($param): Foo
	{

	}

	public function dooFoo($param) : Foo
	{

	}

	public function doooFoo($param)  : Foo
	{

	}

}
