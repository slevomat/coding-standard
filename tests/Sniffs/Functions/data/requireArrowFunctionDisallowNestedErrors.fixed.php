<?php // lint >= 7.4

$a = fn ($aa) => $aa + 1;

$b = fn ($bb) => $bb + $a;

$c = function ($cc) {
	return fn () => $cc + 1;
};
