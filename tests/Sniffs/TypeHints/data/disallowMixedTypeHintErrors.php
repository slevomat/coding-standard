<?php

/**
 * @property mixed[] $property
 */
class Whatever
{

	/** @var (boolean|null|mixed)[] */
	public $a;

	/** @var integer&boolean<boolean, mixed> */
	public $b;

	/** @var mixed&(integer|float) */
	public $c;

	/** @var mixed|(float&integer) */
	public $d;

	/** @var mixed[][][] */
	public $e;

	/** @var (integer|mixed)[][][] */
	public $f;

	/** @var mixed|(\Foo<mixed>&boolean)[] */
	public $g;

	/** @var \Foo<\Boo<mixed>> */
	public $h;

}

class CallableType
{

	/**
	 * @return callable(mixed $bool): mixed
	 */
	public function returnsCallable()
	{

	}

}

/** @var array{int, mixed} $arrayShape1 */
$arrayShape1 = [];

/** @var array{foo: mixed} $arrayShape2 */
$arrayShape2 = [];
