<?php

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
