<?php // lint >= 7.4

class Foo
{

	/** @var string */
	private $foo;

	/** @var string */
	static public $static1;

	/** @var string */
	public static $static2;

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

		foreach ($ee->iterate() as $ff) {
			/** @var \DateTimeImmutable $ff */
			$fff = $ff->format('Ymd');
		}

		foreach ($eee as $fff) {
			/** @var \DateTimeImmutable $fff */
			[$ffff] = $fff->format('Ymd');
		}

		foreach ($eeee as $ffff) {
			/** @var \DateTimeImmutable $ffff */
			[$fffff] = $ffff->format('Ymd');
		}

		/** @var \DateTimeImmutable $h */
		while ($h = current($g)) {

		}

		while ($hh = current($gg)) {
			/** @var \DateTimeImmutable $hh */
			$hhh = 'anything';
		}

		/* TODO */
		$i = 'i';

		/* @variable */
		$j = 'j';

		/** @var string $k */
		[$k] = ['k'];

		/** @var string $l */
		[$l] = ['l'];

		/**
		 * @var string $mm
		 * @var int $mmm
		 */
		foreach ($m as [$mm, $mmm]) {

		}

		/** @var int $nn */
		/** @var float $nnn */
		foreach ($n as $nn => $nnn) {

		}

		foreach ($o as $oo => $ooo) {
			/** @var int $oo */
			/** @var float $ooo */
		}

		array_map(static function ($p): int {
			/** @var string $p */
		}, []);

		/** @var string $q */
		array_map(static function ($q): int {
		}, []);

		/** @var string[]|null $r */
		static $r;

		/** @var string $s */
		$ss = function ($s) {
		};

		/** @var int $t */
		fn &(array &$t) => $t;

		/** @var int $tt */
		array_map(fn ($tt): int => $tt, []);

		/** @var callable(string) : int $callable1 */
		$callable1 = static function(string $x) : int{return 1;};
		/** @var callable(string): int $callable2 */
		$callable2 = static function(string $x) : int{return 1;};
		/** @var callable(string):int $callable3 */
		$callable3 = static function(string $x) : int{return 1;};
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

		/** @var list<array{name: string, autoload: array{classmap: array<int, string>, files: array<int, string>, psr-4: array<string, array<int, string>>, psr-0: array<string, array<int, string>>}}>|null $i */
		$i = [];
	}

}
