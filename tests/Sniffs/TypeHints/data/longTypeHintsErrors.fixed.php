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
 * @method method(?int $m = null, ?bool $m2, $m3)
 * @method bool[]|array method(bool $m)
 */
class Boo
{

}
