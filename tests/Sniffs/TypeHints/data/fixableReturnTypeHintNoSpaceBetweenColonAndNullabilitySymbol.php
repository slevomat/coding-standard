<?php // lint >= 7.4

function foo():?Foo
{

}

$callback = function():?Foo
{

};

$callback = fn ($a):?Foo => $a;

interface Foo
{

	public function doFoo($param):?Foo;

}

class FooBar implements Foo
{

	public function doFoo($param):?Foo
	{

	}

}
