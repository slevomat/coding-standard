<?php

use Something;
use Something\Anything;

$x = $a ?: null;

$y = $b ?: false;

$z = [$a ?: null];

$foo = [
	$a ?: 0 => true,
];

$foo = something(
	$a ?: null,
	$b ?: 0
);

$foo = $a ?: $b ?? 0;

$array[$a ?: 0] = null;
$array[$$a ?: 0] = null;

$x = true ?: false;

class Whatever
{

	public function __construct($parameter)
	{
		$x = self::$a ?: null;
		$x = self::CONSTANT ?: null;
		$x = static::$a ?: null;
		$x = self::$$parameter ?: null;
		$x = self::${'a'} ?: null;
		$x = self::${'a'}[0] ?: null;
		$x = Anything::$a ?: null;
		$x = Something\Anything::$a ?: null;
		$x = \Something\Anything::$a ?: null;
		$x = self::$a::$b ?: null;
		$x = $this::$a ?: null;
		$x = $this::CONSTANT ?: null;
		$x = $this->a ?: null;
		$x = $this->$$parameter ?: null;
		$x = $this->{'a'} ?: null;
		$x = $this->${'a'}[0]->$$b[1][2]::$c[3][4][5]->{" $d"} ?: null;

		$x = a() ?: null;

		if (a() ?: null) {

		}

		if (a() ?: null) {

		}
	}

}
