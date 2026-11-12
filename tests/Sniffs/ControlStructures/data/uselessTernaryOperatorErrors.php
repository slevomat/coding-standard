<?php

function () {
	return true ? true : false;
};

function () {
	$a = false ? true : false;
};

function ($bool) {
	$b = $bool ? true : false;
};

function ($bool) {
	$c = ($bool ? false : true);
};

function ($array) {
	echo count($array) > 0 ? true : false;
};

function ($array) {
	if (count($array) > 0 ? false : true) {

	}
};

function () {
	$d = [10 === 100 ? false : true];
};

function () {
	$e = [
		1,
		10 === 100 ? false : true,
		3,
	];
};

function () {
	$f = [
		1 => 10 === 100 ? false : true,
	];
};

function () {
	switch (true) {
		case 10 === 100 ? false : true:
			break;
	}
};

function () {
	return true ?: false;
};

function () {
	return false ?: true;
};

function ($request) {
	$withFiles = \count($request->request->all()) !== 0
		&& \array_key_exists('with_files', $request->request->get('form'))
		&& $request->request->get('form')['with_files'] === '1' ? true : false;

	return $withFiles;
};

function ($a) {
	$r = $a === 1 ? true : false ?? null and sideEffect();
};

function ($a) {
	$r = (print $a === 1) ? true : false;
};

function ($a) {
	$r = $a === 1 ?: false ?? null ?: sideEffect();
};

function ($a, $b) {
	$r = $a === 1 ? true : false ?? (function () use ($b) { return ['default' => 'fallback']; })();
};

function ($key, $a) {
	$r = [$key === 'x' => $a ? true : false];
};

function ($a) {
	$r = $a === 1 ? true : false ?? null ?>Hello<?php echo 'retained';
};

function ($a) {
	$r = $a === 1 ? true : false ?? function (): string { return 'fallback'; };
};

function ($a) {
	$r = $a === 1 ? true : false ?? static fn (): string => 'fallback';
};

function ($a) {
	$r = $a === 1 ? true : false ?? function () { yield 1; };
};

function ($a) {
	$r = $a === 1 ? true : false ?? yield 1;
};

function ($a) {
	foo($a === 1 ? true : false ?? fn () => 1, 2);
};

function ($flag) {
	$r = $flag && function (): void {} !== null ? false : true;
};

function ($flag) {
	$r = $flag && static function (): int {} !== null ? false : true;
};

function ($flag) {
	$r = $flag && new class () { public function foo(): void {} } !== null ? false : true;
};

function ($a) {
	$r = [$a === 1 ? true : false ?? [1] => 2];
};

function ($flag, $a) {
	$r = $flag && [0 => $a === 1, 1 => 'fallback'] ? false : true;
};
