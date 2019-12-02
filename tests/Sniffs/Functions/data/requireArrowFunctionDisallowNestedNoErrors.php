<?php // lint >= 7.4

$a = function ($aa) {
	if ($aa) {
		return true;
	} else {
		return false;
	}
};

$b = function () use (&$a) {
	return $a + 1;
};

$c = function () {
	echo 'something';
};

$d = function () {
	return fn () => true;
};
