<?php

function foo(?\DateTimeImmutable & $dateTime = null)
{

}

$callback = function (?string $string = null) {

};

trait Foo
{

	public function doFoo(?int $int = null)
	{

	}

}

class FooBar extends \stdClass
{

	public function valid(?bool $bool = null, $noTypehint = null, ?int $int, string $a = 'default', ?string $b = 'null')
	{

	}

	public function myself(?self $self = null)
	{

	}

	public function array(?array $array = null)
	{

	}

	public function parent(?parent $parent = null)
	{

	}

	public function callable(?callable $callable = null)
	{

	}

	public function withNullableParamBefore(float $float, ?\Foo\Bar\Baz $param1 = null)
	{

	}

	public function withParamAfter(?bool $param2 = null, array ...$ellipsis)
	{

	}

	public function reference(?float & $ref = null, ...$test)
	{

	}

}
