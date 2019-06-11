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
 * @method method3(?\Foo<(null|int)> $m)
 */
class Boo
{

}

class IntersectionAndGeneric
{

	/** @var (bool|null|int)[] */
	public $a;

	/** @var string&(null|int|float) */
	public $b;

	/** @var (int|null|bool)[][][] */
	public $c;

	/** @var int|null|(string<foo>&bool)[] */
	public $d;

	/** @var \Foo<(null|int), (null|bool)> */
	public $e;

	/** @var \Foo<\Foo<null|int>> */
	public $f;

}

class CallableType
{

	/**
	 * @return callable((null|bool) $bool): (null|int)
	 */
	public function returnsCallable()
	{

	}

}

/** @var array{int, (null|int), (null|bool)} $arrayShape1 */
$arrayShape1 = [];

/** @var array{foo: (null|int)} $arrayShape2 */
$arrayShape2 = [];
