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

		/** @var iterable|array|\Traversable $d Lorem ipsum */
		$d = [];

		/** @var string $f */
		foreach ($e as $f) {

		}

		/** @var \DateTimeImmutable $h */
		while ($h = current($g)) {

		}

		/* TODO */
		$i = 'i';

		/* @variable */
		$j = 'j';
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
		/** @var (boolean | null | integer)[] $a */
		$a = null;

		/** @var integer & boolean<boolean, integer> $b */
		$b = 0;

		/** @var string & (integer | float) $c */
		$c = 'string';

		/** @var string | (float&integer) $d */
		$d = 'string';

		/** @var boolean[][][] $e */
		$e = [];

		/** @var (integer | boolean)[][][] $f */
		$f = [];

		/** @var integer|(string<foo> & boolean)[] $g */
		$g = 0;

		/** @var \Foo<\Boo<integer, boolean>> $h */
		$h = [];
	}

}
