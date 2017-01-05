<?php

function foo():Foo
{

}

function fooo():  Foo
{

}

function foooo() :Foo
{

}

function fooooo() : Foo
{

}

$callback = function():Foo
{

};

$callback = function fooo() use ($test):  Foo
{

};

$callback = function foooo() :Foo
{

};

$callback = function fooooo() use ($test) : Foo
{

};

interface Foo
{

	public function doFoo($param):Foo;

	public function doBar($param):\Bar;

	public function doBaz($param):\Foo\Bar;

	public function dooFoo($param):  Foo;

	public function dooBar($param):  \Bar;

	public function dooBaz($param):  \Foo\Bar;

	public function doFoo($param) :Foo;

	public function doBar($param) :\Bar;

	public function doBaz($param) :\Foo\Bar;

	public function dooFoo($param) :  Foo;

	public function dooBar($param) :  \Bar;

	public function dooBaz($param) :  \Foo\Bar;

}

class FooBar implements Foo
{

	public function doFoo($param):Foo
	{

	}

	public function doBar($param):\Bar
	{

	}

	public function doBaz($param):\Foo\Bar
	{

	}

	public function dooFoo($param):  Foo
	{

	}

	public function dooBar($param):  \Bar
	{

	}

	public function dooBaz($param):  \Foo\Bar
	{

	}

	public function doooFoo($param) :Foo
	{

	}

	public function doBar($param) :\Bar
	{

	}

	public function doooBaz($param) :\Foo\Bar
	{

	}

	public function dooooFoo($param) :  Foo
	{

	}

	public function dooooBar($param) :  \Bar
	{

	}

	public function dooooBaz($param) :  \Foo\Bar
	{

	}

}
