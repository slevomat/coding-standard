<?php

return $z;

$a = null;

function returnWithoutVariable() {
	return true;
}

function varibleOutsideScope() {
	return $a;
}

function moreComplexReturn() {
	$b = 1;
	return $b + 1;
}

function notAssignment() {
	$c + 1;
	return $c;
}

function sameVariableAfterReturn() {
	$d = 0;

	if (true) {
		return $d;
	}

	$d = 1;
}

function differentVariable() {
	$e = 10;
	return $f;
}

function staticVariable() {
	static $g = null;
	return $g;
}

function withDocComment() {
	/** @var string $h */
	$h = 'h';
	return $h;
}
