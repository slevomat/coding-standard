<?php declare(strict_types = 1);

class Foo
{

	public function simpleTypeHints($var, array $array, bool $bool, int $int, float $float, string $string, callable $callable)
	{
		$var->foo();
	}

}
