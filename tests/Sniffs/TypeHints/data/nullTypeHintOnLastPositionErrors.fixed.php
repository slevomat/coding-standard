<?php

class Whatever
{

	/**
	 * @var bool|null
	 */
	private $multiLineAnnotation;

	/** @var bool|null */
	private $singleLineAnnotation;

	/**
	 * @param string|null $param
	 */
	public function paramAnnotation($param)
	{
		/** @var float|int|null $number */
		$inlineAnnotation = 0.0;

		/** @var $invalidAnnotation null|float|int */
		$inlineAnnotationInSecondFormat = 0.0;
	}

	/**
	 * @return string|null
	 */
	public function returnAnnotation()
	{

	}

	/**
	 * @return string|NULL
	 */
	public function uppercasedNull()
	{

	}

	/**
	 * @return float|int|int[]|null
	 */
	public function nullInTheMiddle()
	{

	}

}

/**
 * @property bool|null $property
 * @property-read int|null $propertyRead
 * @property-write int|null $propertyWrite
 * @method bool|null method(int $m, bool ...$m2)
 * @method bool[]|array|null method2(bool $m)
 * @method method3(?\Foo<(int|null)> $m)
 */
class Boo
{

}

class IntersectionAndGeneric
{

	/** @var (bool|int|null)[] */
	public $a;

	/** @var string&(int|float|null) */
	public $b;

	/** @var (int|bool|null)[][][] */
	public $c;

	/** @var int|(string<foo>&bool)[]|null */
	public $d;

	/** @var \Foo<(int|null), (bool|null)> */
	public $e;

	/** @var \Foo<\Foo<(int|null)>> */
	public $f;

}

class CallableType
{

	/**
	 * @return callable((bool|null) $bool): (int|null)
	 */
	public function returnsCallable()
	{

	}

}

/** @var array{int, (int|null), (bool|null)} $arrayShape1 */
$arrayShape1 = [];

/** @var array{foo: (int|null)} $arrayShape2 */
$arrayShape2 = [];
