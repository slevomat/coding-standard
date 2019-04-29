<?php

$a = 1 & 0;
$b = $a & $a;

function &  () {
	$a = true;
	return $a;
};

function &  foo() {
	$a = true;
	return $a;
}

function ($no, &  $a) {

};

function boo($no, &  $a) {

}

$b = false;
function () use (&  $b) {

};

$c = &  $b;
$d = [
	&  $b,
	1 => &  $c,
	&  $b,
];

foreach ([] as &  $e) {
}
