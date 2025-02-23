<?php // lint >= 8.2

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

	abstract public function parametersWithWeirdDefinition(string$string,$int,bool$bool=true,$float,callable$callable,$array=[],\FooNamespace\FooClass$object);

	public function unionTypeHints(string|int $a, int|false $b, null|int $c, string | int | float $d)
	{}

	public function dnf(
		(A&B)|C|D $a,
		A|(B&C)|D $b,
		A|B|(C&D) $c,
	) {

	}

}
