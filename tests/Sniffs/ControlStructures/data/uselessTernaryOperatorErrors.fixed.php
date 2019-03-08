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
