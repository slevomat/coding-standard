<?php // lint >= 7.4

$a = function ($aa) {
	return $aa + 1;
};

$b = function ($bb) use ($a) {
	return $bb + $a;
};

$c = function ($cc) {
	return function () use ($cc) {
		return $cc + 1;
	};
};
