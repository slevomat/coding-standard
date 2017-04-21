<?php

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
