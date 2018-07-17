<?php

use Something;
use Something\Anything;

$x = $a ? $a : null;

$y = !$b ? false : $b;

$z = [$a ? $a : null];

$foo = [
	$a ? $a : 0 => true,
];

$foo = something(
	$a ? $a : null,
	!$b ? 0 : $b
);

$foo = $a ? $a : $b ?? 0;

$array[$a ? $a : 0] = null;
$array[$$a ? $$a : 0] = null;

$x = true ? true : false;

class Whatever
{

	public function __construct($parameter)
	{
		$x = self::$a ? self::$a : null;
		$x = self::CONSTANT ? self::CONSTANT : null;
		$x = static::$a ? static::$a : null;
		$x = self::$$parameter ? self::$$parameter : null;
		$x = self::${'a'} ? self::${'a'} : null;
		$x = self::${'a'}[0] ? self::${'a'}[0] : null;
		$x = Anything::$a ? Anything::$a : null;
		$x = Something\Anything::$a ? Something\Anything::$a : null;
		$x = \Something\Anything::$a ? \Something\Anything::$a : null;
		$x = self::$a::$b ? self::$a::$b : null;
		$x = $this::$a ? $this::$a : null;
		$x = $this::CONSTANT ? $this::CONSTANT : null;
		$x = $this->a ? $this->a : null;
		$x = $this->$$parameter ? $this->$$parameter : null;
		$x = $this->{'a'} ? $this->{'a'} : null;
		$x = !$this->${'a'}[0]->$$b[1][2]::$c[3][4][5]->{" $d"} ? null : $this->${'a'}[0]->$$b[1][2]::$c[3][4][5]->{" $d"};

		$x = a() ? a() : null;

		if (a() ? a() : null) {

		}

		if (!a() ? null : a()) {

		}
	}

}
