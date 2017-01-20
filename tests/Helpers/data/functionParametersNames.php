<?php // lint >= 7.1

namespace FooNamespace;

abstract class FooClass
{

	public function allParametersWithTypeHints(string $string, int $int = 10, bool $bool, float $float = 0.0, callable $callable, array $array, \FooNamespace\FooClass $object = null)
	{

	}

	final public function allParametersWithoutTypeHints($string = '', $int, $bool, $float, $callable, $array, $object = null)
	{

	}

	abstract public function someParametersWithoutTypeHints(string $string, $int, bool $bool = true, $float, callable $callable, $array = [], \FooNamespace\FooClass $object);

	public function allParametersWithNullableTypeHints(?string $string, ?int $int = 10, ?bool $bool, ?float $float = 0.0, ?callable $callable, ?array $array, ?\FooNamespace\FooClass $object = null)
	{

	}

	abstract public function someParametersWithNullableTypeHints(?string $string, int $int, ?bool $bool = true, float $float, ?callable $callable, array $array = [], ?\FooNamespace\FooClass $object);

}
