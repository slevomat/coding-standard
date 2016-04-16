<?php

namespace FooNamespace;

abstract class FooClass
{

	public function allParametersWithTypeHints(string $string, int $int, bool $bool, float $float, callable $callable, array $array, \FooNamespace\FooClass $object)
	{

	}

	final public function allParametersWithoutTypeHints($string, $int, $bool, $float, $callable, $array, $object)
	{

	}

	abstract public function someParametersWithoutTypeHints(string $string, $int, bool $bool, $float, callable $callable, $array, \FooNamespace\FooClass $object);

}
