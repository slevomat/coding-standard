<?php

$a = isset($_GET['a']) ? $_GET['a'] : 'a';

$b = isset($bb) ? $bb : 'bb';

$c = isset($cc['c']) ? $cc['c'] : 'c';

$d = $e !== null ? $e : 'e';
$dd = null !== $ee ? $ee : 'ee';

$f = $g === null ? 'g' : $g;
$ff = null === $gg ? 'gg' : $gg;

$h = isset($this->${'a'}[0]->$$b[1][2]::$c[3][4][5]->xxx->{" $d"}) ? $this->${'a'}[0]->$$b[1][2]::$c[3][4][5]->xxx->{" $d"} : 1;

$i = $this->${'a'}[0]->$$b[1][2]::$c[3][4][5]->xxx->{" $d"} !== null ? $this->${'a'}[0]->$$b[1][2]::$c[3][4][5]->xxx->{" $d"} : 0;
$j = $this->${'a'}[0]->$$b[1][2]::$c[3][4][5]->xxx->{" $d"} === null ? false : $this->${'a'}[0]->$$b[1][2]::$c[3][4][5]->xxx->{" $d"};

$k = $this
		->${'a'}[0]->$$b[1][2]
		::$c[3][4][5]->{" $d"} !== null
	? $this
		->${'a'}[0]
		->$$b[1][2]
		::$c[3][4][5]
		->{" $d"}
	: true;

$l = \Whatever\Something::$anything !== null ? \Whatever\Something::$anything : 1;

$m = $object->anything === null ? 0 : $object->anything;

$n = ($something === null ? false : $something);

$o[$something === null ? true : $something] = true;

$p = doSomething()() === null ? false : doSomething()();

$q[$something === null ? ($anything !== null ? $anything : 'default') : $something] = true;
$r[$something === null ? ($anything !== false ? $anything : 'default') : $something] = true;

function ($options) {
	return (int) isset($options['value']) ? $options['value'] : 1;
};

$s = $a === null ? $b ? 'x' : 'y' : $a;
$t = $a === null ? [$b][0] ? 'x' : 'y' : $a;
$u = $a === null ? ${'b'} ? 'x' : 'y' : $a;
echo isset($a) ? $a : 'default';
echo $a === null ? 'default' : $a;
$out .= $a === null ? 'default' : $a;
$out .= isset($a) ? $a : 'default';
$v = [isset($a) ? $a : [1, 2], 'foo'];

$s2 = $a === null ? $b ? 'x' : 'y' /* keep */ : $a;
$s3 = $a === null ? $b ? 'x' : 'y' // keep
	: $a;

function () use ($a) {
	yield isset($a) ? $a : 'default';
	yield from isset($a) ? $a : 'default';
	yield $a === null ? 'default' : $a;
	yield from $a === null ? 'default' : $a;
};

