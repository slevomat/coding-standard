<?php

class Whatever extends Anything
{

	public function __construct($parameter)
	{
		$x = (self::$a);
		$x = (static::$a);
		$x = (self::$$parameter);
		$x = (parent::${'a'});
		$x = (self::${'a'}[0]);
		$x = (Anything::$a);
		$x = (Something\Anything::$a);
		$x = (\Something\Anything::$a);
		$x = (self::$a::$b);
		$x = ($this::$a);
		$x = ($this->a);
		$x = ($this->$$parameter);
		$x = ($this->{'a'});
		$x = ($$parameter);
		$x = ($this->${'a'}[0]->$$b[1][2]::$c[3][4][5]->{" $d"});
		$x = ($this->${'a'}[0]->$$b[1][2]::$c[3][4][5]->{" $d"}());
		$x = ($this->${'a'}[0]->$$b[1][2]::$c[3][4][5]->{" $d"}()()()());
		$x = (!$this->${'a'}[0]->$$b[1][2]::$c[3][4][5]->{" $d"}()()()());
		$x = (isset($xxx));
		$x = (!isset($xxx));
		$x = (empty($xxx));
		$x = (!empty($xxx));
		$x = ! (in_array($foo, ['bar', 'foo']));
		$x = (intval($foo));
	}

}

$x = ($y !== null) ? true : false;
$a = ($b) ? 1 : 0;
$c = (   $d    ) ? 1 : 0;

switch (true) {
	case ($boo):
	case ($boo === true):
	case ($boo ? true : false):
	case ($boo) ? true : false:
}

function () {
	return [
		'a' => ('aa'),
	];
};

$x = ($x + 1);

$x = ($y + $yy) - $z;
$x = ($y * $yy) / $z;
$x = ((100 / 50) * 100);
$x = ($a + $b * 3);
$x = $b + (100 - $c);
$x = $b * (100 / $c);

function () {
	return [
		'a' => ('aa' . 'bb'),
	];
};
