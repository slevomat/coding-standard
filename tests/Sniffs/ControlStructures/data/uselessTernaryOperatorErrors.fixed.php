<?php

function () {
	return true;
};

function () {
	$a = false;
};

function ($bool) {
	$b = $bool ? true : false;
};

function ($bool) {
	$c = ($bool ? false : true);
};

function ($array) {
	echo count($array) > 0;
};

function ($array) {
	if (count($array) <= 0) {

	}
};

function () {
	$d = [10 !== 100];
};

function () {
	$e = [
		1,
		10 !== 100,
		3,
	];
};

function () {
	$f = [
		1 => 10 !== 100,
	];
};

function () {
	switch (true) {
		case 10 !== 100:
			break;
	}
};

function () {
	return true;
};

function () {
	return false;
};

function ($request) {
	$withFiles = \count($request->request->all()) !== 0
		&& \array_key_exists('with_files', $request->request->get('form'))
		&& $request->request->get('form')['with_files'] === '1';

	return $withFiles;
};

function ($a) {
	$r = $a === 1 and sideEffect();
};

function ($a) {
	$r = (print $a === 1) ? true : false;
};

function ($a) {
	$r = $a === 1 ?: sideEffect();
};

function ($a, $b) {
	$r = $a === 1;
};

function ($key, $a) {
	$r = [$key === 'x' => $a ? true : false];
};

function ($a) {
	$r = $a === 1 ?>Hello<?php echo 'retained';
};

function ($a) {
	$r = $a === 1;
};

function ($a) {
	$r = $a === 1;
};

function ($a) {
	$r = $a === 1;
};

function ($a) {
	$r = $a === 1 ? true : false ?? yield 1;
};

function ($a) {
	foo($a === 1, 2);
};

function ($flag) {
	$r = !$flag || function (): void {} === null;
};

function ($flag) {
	$r = !$flag || static function (): int {} === null;
};

function ($flag) {
	$r = !$flag || new class () { public function foo(): void {} } === null;
};

function ($a) {
	$r = [$a === 1 => 2];
};

function ($flag, $a) {
	$r = !$flag || ![0 => $a === 1, 1 => 'fallback'];
};
