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
