<?php

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
