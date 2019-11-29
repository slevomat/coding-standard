<?php // lint >= 7.4

function &() {
	$a = true;
	return $a;
};

function &foo() {
	$a = true;
	return $a;
}

function ($no, &$a) {

};

function boo($no, &$a) {

}

$b = false;
function () use (&$b) {

};

$c = &$b;
$d = [
	&$b,
	1 => &$c,
	&$b,
];

foreach ([] as &$e) {
}

fn &(array &$f) => $f;
