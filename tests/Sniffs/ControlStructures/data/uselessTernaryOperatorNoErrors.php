<?php

function ($boo) {
	return $boo ?: true;
};

function () {
	$a = [true ? true : (false)];
	return $a;
};

function ($a, $b) {
	return true ? $a : $b;
};

function () {
	return true ? true && doSomething() : false;
};

function ($a) {
	return true ? false : $a;
};

function ($a) {
	return true ?: $a;
};
