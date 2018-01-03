<?php

function foo():   ?Foo
{

}

$callback = function():   ?Foo
{

};

interface Foo
{

	public function doFoo($param):     ?Foo;

}

class FooBar implements Foo
{

	public function doFoo($param):   ?Foo
	{

	}

}
