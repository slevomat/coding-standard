<?php

class Whatever
{

	/**
	 * @var null|bool
	 */
	private $multiLineAnnotation;

	/** @var null|bool */
	private $singleLineAnnotation;

	/**
	 * @param null|string $param
	 */
	public function paramAnnotation($param)
	{
		/** @var null|float|int $number */
		$inlineAnnotation = 0.0;

		/** @var $invalidAnnotation null|float|int */
		$inlineAnnotationInSecondFormat = 0.0;
	}

	/**
	 * @return null|string
	 */
	public function returnAnnotation()
	{

	}

	/**
	 * @return NULL|string
	 */
	public function uppercasedNull()
	{

	}

	/**
	 * @return float|null|int|int[]
	 */
	public function nullInTheMiddle()
	{

	}

}

/**
 * @property null|bool $property
 * @property-read null|int $propertyRead
 * @property-write null|int $propertyWrite
 * @method null|bool method(int $m, bool ...$m2)
 * @method bool[]|null|array method2(bool $m)
 */
class Boo
{

}
