<?php

/**
 * @param integer $a
 * @return boolean
 */
function doSomething($a)
{
	return true;
}

class Foo
{

	/** @var integer|null */
	private $integer = 0;

	/**
	 * Boolean
	 * With
	 * Long
	 * Destription
	 *
	 * @var boolean
	 */
	private $boolean = true;

	/**
	 * Array
	 * @var integer[]|boolean[]|null
	 */
	public $array;

	/**
	 * @param Boolean|null $a
	 * @param null|boolean
	 * @param Integer|bool $c
	 * @return integer|null
	 */
	public function doSomething($a, $b, $c)
	{
		return 0;
	}

	public function inlineDocComment($values)
	{
		/** @var boolean|integer|string $value */
		foreach ($values as $value) {

		}
	}

}

/**
 * @property boolean $property
 * @property-read integer $propertyRead
 * @property-write integer[] $propertyWrite
 * @method boolean method(integer $m, boolean ...$m2)
 * @method method2(?integer $m = null, ?boolean $m2, $m3)
 * @method boolean[]|array method3(?Boolean<integer> $m)
 */
class Boo
{

}

/**
 * @property integer&boolean $property
 */
class IntersectionAndGeneric
{

	/** @var (boolean|null|integer)[] */
	public $a;

	/** @var integer&boolean<boolean, integer> */
	public $b;

	/** @var string&(integer|float) */
	public $c;

	/** @var string|(float&integer) */
	public $d;

	/** @var boolean[][][] */
	public $e;

	/** @var (integer|boolean)[][][] */
	public $f;

	/** @var integer|(string<foo>&boolean)[] */
	public $g;

	/** @var \Foo<\Boo<integer, boolean>> */
	public $h;

}

class CallableType
{

	/**
	 * @return callable(boolean $bool): (integer|float)
	 */
	public function returnsCallable()
	{

	}

}

/**
 * @return boolean - true - if some cond
 *                   false - if some other cond
 */
function multilineDescription() {

}

/** @var array{integer, boolean} $arrayShape1 */
$arrayShape1 = [];

/** @var array{foo: integer, bar: boolean} $arrayShape2 */
$arrayShape2 = [];
