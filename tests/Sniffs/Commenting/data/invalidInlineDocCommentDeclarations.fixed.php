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

		/** @var string $k */
		list($k) = ['k'];

		/** @var string $l */
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
