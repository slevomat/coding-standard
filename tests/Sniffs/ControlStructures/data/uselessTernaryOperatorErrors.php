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
