<?php // lint >= 7.1

function foo(\DateTimeImmutable & $dateTime = null)
{

}

$callback = function (string $string = null) {

};

interface Foo
{

	public function doFoo(int $int = null);

}

class FooBar
{

	public function invalid(bool $bool = null)
	{

	}

	public function myself(self $self = null)
	{

	}

	public function array(array $array = null)
	{

	}

	public function parent(parent $parent = null)
	{

	}

	public function callable(callable $callable = null)
	{

	}

	public function withNullableParamBefore(?float $float, \Foo\Bar\Baz $param1 = null)
	{

	}

	public function withParamAfter(bool $param2 = null, ?array & ... $ellipsis)
	{

	}

	public function reference(float & $ref = null)
	{

	}

}
