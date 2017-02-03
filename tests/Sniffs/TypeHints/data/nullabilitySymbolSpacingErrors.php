<?php // lint >= 7.1

function foo(?   \DateTimeImmutable $param): ? \Foo\Boo\Doo
{

}

$callback = function(? string $param): ?     self
{

};

interface Foo
{

	public function doFoo(?    int $param): ? array;

}

class FooBar implements Foo
{

	public function doBar(? bool $param): ?  callable
	{

	}

}
