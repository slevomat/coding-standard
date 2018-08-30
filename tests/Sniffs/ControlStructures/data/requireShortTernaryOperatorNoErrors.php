<?php

$x = $a ?: true;

sprintf('%s', $x ?: 'string');

$z = isset($x) ?: false;

$y = $foo !== null ? $foo : null;

$yy = null !== $foo ? $foo : null;

$foo = $boo
	? something()
	: somethingElse();

$foo = $boo
	? $doo
	: false;

$foo = !$boo
	? $boo
	: $doo;

$foo = $boo
	? $doo
	: $boo;

$foo = $zz < $id ? $id : 0;

$foo = $array ? $array[0] : '&';

$foo = !$boo ? 0 : $boo[0];

$array[$$a ?: 0] = null;

$x = true ?: false;

$x = $xx && $yy ? $yy : false;

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
	}

}
