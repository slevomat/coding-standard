<?php

namespace FooNamespace;

use ArrayIterator;
use DateTimeImmutable;

/**
 * @param int $a
 */
function simpleTypeHint($a)
{
}

/**
 * @param \Foo $a
 */
function fullyQualifiedClassTypeHint($a)
{
}

/**
 * @param DateTimeImmutable $a
 */
function usedClassTypeHint($a)
{
}

/**
 * @param resource $a
 */
function unofficialTypeHint($a)
{
}

/**
 * @param int|null $a
 */
function nullableSimpleTypeHint($a)
{
}

/**
 * @param null|int $a
 */
function nullableFirstSimpleTypeHint($a)
{
}

/**
 * @param \Foo|null $a
 */
function nullableFullyQualifiedClassTypeHint($a)
{
}

/**
 * @param DateTimeImmutable|null $a
 */
function nullableUsedClassTypeHint($a)
{
}

/**
 * @param resource|null $a
 */
function nullableUnofficialTypeHint($a)
{
}

/**
 * @param mixed[]|array $a
 */
function arrayTypeHint($a)
{
}

/**
 * @param array|mixed[] $a
 */
function arrayTypeHintFirst($a)
{
}

/**
 * @param mixed[]|iterable $a
 */
function iterableTypeHint($a)
{
}

/**
 * @param mixed[]|\Traversable $a
 */
function travesableFullyQualifiedTypeHint($a)
{
}

/**
 * @param mixed[]|ArrayIterator $a
 */
function travesableUsedTypeHint($a)
{
}

/**
 * @param string $a
 */
function reference(&$a)
{

}

/**
 * @param string ...$a
 */
function varadic(...$a)
{

}

/**
 * @param string $a
 * @param bool $b
 * @param int $c
 * @param float $d
 * @param callable $e
 * @param array $f
 * @param iterable $g
 */
function more(string $a, $b, int $c, $d, callable $e, $f, iterable $g)
{

}

/**
 * @param string Description
 * @param bool|null
 */
function withoutTypeHintAndWithInvalidAnnotation($a, $b)
{
}

/**
 * @param string $b
 * @param bool|null
 * @param float $c
 */
function oneWithoutTypeHintAndWithInvalidAnnotation(string $a, $b, float $c)
{
}

/**
 * @param mixed[] $a
 */
function traversableMixed($a)
{

}

/**
 * @param string[] $a
 */
function traversableStrings($a)
{

}

/**
 * @param \DateTimeImmutable[] $a
 */
function traversableDateTimeImmutable($a)
{

}

/**
 * @param $a string
 */
function invalidAnnotation($a)
{

}

abstract class Foo
{

	/**
	 * @param int $a
	 */
	public function simpleTypeHint($a)
	{
	}

	/**
	 * @param \Foo $a
	 */
	public function fullyQualifiedClassTypeHint($a)
	{
	}

	/**
	 * @param DateTimeImmutable $a
	 */
	public function usedClassTypeHint($a)
	{
	}

	/**
	 * @param resource $a
	 */
	public function unofficialTypeHint($a)
	{
	}

	/**
	 * @param int|null $a
	 */
	private function nullableSimpleTypeHint($a)
	{
	}

	/**
	 * @param null|int $a
	 */
	private function nullableFirstSimpleTypeHint($a)
	{
	}

	/**
	 * @param \Foo|null $a
	 */
	private function nullableFullyQualifiedClassTypeHint($a)
	{
	}

	/**
	 * @param DateTimeImmutable|null $a
	 */
	private function nullableUsedClassTypeHint($a)
	{
	}

	/**
	 * @param resource|null $a
	 */
	private function nullableUnofficialTypeHint($a)
	{
	}

	/**
	 * @param mixed[]|array $a
	 */
	protected function arrayTypeHint($a)
	{
	}

	/**
	 * @param array|mixed[] $a
	 */
	protected function arrayTypeHintFirst($a)
	{
	}

	/**
	 * @param mixed[]|iterable $a
	 */
	protected function iterableTypeHint($a)
	{
	}

	/**
	 * @param mixed[]|\Traversable $a
	 */
	protected function travesableFullyQualifiedTypeHint($a)
	{
	}

	/**
	 * @param mixed[]|ArrayIterator $a
	 */
	protected function travesableUsedTypeHint($a)
	{
	}

	/**
	 * @param string $a
	 */
	public function reference(&$a)
	{

	}

	/**
	 * @param string ...$a
	 */
	private function varadic(...$a)
	{

	}

	/**
	 * @param string $a
	 * @param bool $b
	 * @param int $c
	 * @param float $d
	 * @param callable $e
	 * @param array $f
	 * @param iterable $g
	 */
	public function more(string $a, $b, int $c, $d, callable $e, $f, iterable $g)
	{

	}

	/**
	 * @param string $a
	 */
	abstract function abstractMethod($a);

	/**
	 * @param string $a
	 */
	public static function staticMethod($a)
	{

	}

	/**
	 * @param string Description
	 * @param bool|null
	 */
	public function withoutTypeHintAndWithInvalidAnnotation($a, $b)
	{
	}

	/**
	 * @param string $b
	 * @param bool|null
	 * @param float $c
	 */
	public function oneWithoutTypeHintAndWithInvalidAnnotation(string $a, $b, float $c)
	{
	}

	/**
	 * @param mixed[] $a
	 */
	public function traversableMixed($a)
	{

	}

	/**
	 * @param string[] $a
	 */
	abstract public function traversableStrings($a);

	/**
	 * @param \DateTimeImmutable[] $a
	 */
	protected function traversableDateTimeImmutable($a)
	{

	}

	/**
	 * @param $a string
	 */
	private function invalidAnnotation($a)
	{

	}

}

class IntersectionAndGeneric
{

	/**
	 * @param (bool|int)[][] $a
	 */
	public function a($a): void
	{
	}

	/**
	 * @param \Foo<string>|null $b
	 */
	public function b($b): void
	{
	}

	/**
	 * @param null[]|\Traversable $c
	 */
	public function c($c): void
	{
	}

	/**
	 * @param array<int> $d
	 */
	public function d($d): void
	{
	}

	/**
	 * @param int[][]|null $e
	 */
	public function e($e): void
	{
	}

}

class CallableType
{

	/**
	 * @param \Closure():array|null $parameter
	 */
	public function callableParameter($parameter): void
	{

	}

	/**
	 * @param array<\Closure():array> $parameter
	 */
	public function traversableCallableParameter($parameter): void
	{

	}

}
