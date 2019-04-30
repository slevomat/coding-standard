<?php

class Foo
{

	/** @var string */
	private $foo;

	public function __construct()
	{
		/** @var $a string[] */
		$a = $this->get();

		/** @see https://www.slevomat.cz */
		$b = null;

		/** @var $c */
		$c = [];

		/** @var $d iterable|array|\Traversable Lorem ipsum */
		$d = [];

		/** @var $f string */
		foreach ($e as $f) {

		}

		/** @var $h \DateTimeImmutable */
		while ($h = current($g)) {

		}

		/* @var $i string */
		$i = 'i';

		/** @var */
		$j = 10;

		/** @var $k string */
		list($k) = ['k'];

		/** @var $l string */
		[$l] = ['l'];
	}

	public function get()
	{
		$a = [];
		return $a;
	}

}

class IntersectionAndGeneric
{

	public function anything()
	{
		/** @var $a (boolean | null | integer)[] */
		$a = null;

		/** @var $b integer & boolean<boolean, integer> */
		$b = 0;

		/** @var $c string & (integer | float) */
		$c = 'string';

		/** @var $d string|(float&integer) */
		$d = 'string';

		/** @var $e boolean[][][] */
		$e = [];

		/** @var $f (integer | boolean)[][][] */
		$f = [];

		/** @var $g integer|(string<foo> & boolean)[] */
		$g = 0;

		/** @var $h \Foo<\Boo<integer, boolean>> */
		$h = [];
	}

}
