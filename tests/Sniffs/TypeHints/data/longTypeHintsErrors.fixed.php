<?php

/**
 * @param int $a
 * @return bool
 */
function doSomething($a)
{
	return true;
}

class Foo
{

	/** @var int|null */
	private $integer = 0;

	/**
	 * Boolean
	 * With
	 * Long
	 * Destription
	 *
	 * @var bool
	 */
	private $boolean = true;

	/**
	 * Array
	 * @var int[]|bool[]|null
	 */
	public $array;

	/**
	 * @param bool|null $a
	 * @param null|boolean
	 * @param int|bool $c
	 * @return int|null
	 */
	public function doSomething($a, $b, $c)
	{
		return 0;
	}

	public function inlineDocComment($values)
	{
		/** @var bool|int|string $value */
		foreach ($values as $value) {

		}
	}

}

/**
 * @property bool $property
 * @property-read int $propertyRead
 * @property-write int[] $propertyWrite
 * @method bool method(int $m, bool ...$m2)
 * @method method2(?int $m = null, ?bool $m2, $m3)
 * @method bool[]|array method3(?bool<int> $m)
 */
class Boo
{

}

/**
 * @property int&bool $property
 */
class IntersectionAndGeneric
{

	/** @var (bool|null|int)[] */
	public $a;

	/** @var int&bool<bool, int> */
	public $b;

	/** @var string&(int|float) */
	public $c;

	/** @var string|(float&int) */
	public $d;

	/** @var bool[][][] */
	public $e;

	/** @var (int|bool)[][][] */
	public $f;

	/** @var int|(string<foo>&bool)[] */
	public $g;

	/** @var \Foo<\Boo<int, bool>> */
	public $h;

}

class CallableType
{

	/**
	 * @return callable(bool $bool): (int|float)
	 */
	public function returnsCallable()
	{

	}

}

/**
 * @return bool - true - if some cond
 * false - if some other cond
 */
function multilineDescription() {

}

/** @var array{int, bool} $arrayShape1 */
$arrayShape1 = [];

/** @var array{foo: int, bar: bool} $arrayShape2 */
$arrayShape2 = [];
