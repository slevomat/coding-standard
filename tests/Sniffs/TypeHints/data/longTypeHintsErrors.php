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
 * @method method(?integer $m = null, ?boolean $m2, $m3)
 * @method boolean[]|array method(Boolean $m)
 */
class Boo
{

}
