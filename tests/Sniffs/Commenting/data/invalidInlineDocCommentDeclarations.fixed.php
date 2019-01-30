<?php

class Foo
{

	/** @var string */
	private $foo;

	public function __construct()
	{
		/** @var string[] $a */
		$a = $this->get();

		/** @see https://www.slevomat.cz */
		$b = null;

		/** @var $c */
		$c = [];

		/** @var iterable|array|\Traversable $d Lorem ipsum */
		$d = [];

		/** @var string $f */
		foreach ($e as $f) {

		}

		/** @var \DateTimeImmutable $h */
		while ($h = current($g)) {

		}

		/** @var string $i */
		$i = 'i';

		/** @var */
		$j = 10;
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

		/** @var string|(float&integer) $d */
		$d = 'string';

		/** @var boolean[][][] $e */
		$e = [];

		/** @var $f (integer | boolean)[][][] */
		$f = [];

		/** @var $g integer|(string<foo> & boolean)[] */
		$g = 0;

		/** @var $h \Foo<\Boo<integer, boolean>> */
		$h = [];
	}

}
