<?php // lint >= 7.1

function foo(?\DateTimeImmutable $param): ?Foo
{

}

$callback = function(?string $param): ?Foo
{

};

interface Foo
{

	public function doFoo(?int $param): ?Foo;

}

class FooBar implements Foo
{

	public function doBar(?bool $param): ?Bar
	{

	}

}
