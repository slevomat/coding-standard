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

/** @var string $unknown */

/** @var string $unknownA */
$a = 'a';

/** @var int $unknownB */
[$b] = [];

/** @var int $unknownC */
list($c) = [];

/** @var int $unknownD */
foreach ([] as $d) {
}

/** @var int $unknownE */
while ($e = next($array)) {

}

$aa = 'aa';
/** @var string $unknownAA */

[$bb] = [];
/** @var int $unknownBB */

list($cc) = [];
/** @var int $unknownCC */

foreach ([] as $dd) {
	/** @var int $unknownDD */
}

while ($ee = next($array)) {
	/** @var int $unknownEE */
}

/** @var string */

function () {
};
/** @var string $unknownX */

/** @var string $noAssignmentX */
$noAssignmentX .= 'string';

/** @var string $noAssignmentY */
[$noAssignmentY];

/** @var string $noAssignmentZ */
while ($foo = next($noAssignmentZ)) {

}

/** @var string $unknownParameter */
function ($whatever) {
};

/** @var $i list<array{name: string, autoload: array{classmap: array<int, string>, files: array<int, string>, psr-4: array<string, array<int, string>>, psr-0: array<string, array<int, string>>}}>|null */
$i = [];
