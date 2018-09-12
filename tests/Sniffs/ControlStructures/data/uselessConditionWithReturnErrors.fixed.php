<?php

function () {
	return true;
};

function () {
	return false;
};

function () {
	return false;
};

function () {
	return true;
};

function () {
	return true;
};

function ($bool) {
	return $bool;
};

function ($e, $number) {
	return false && !($e instanceof Exception) && $number > 0;
};

function () {
	return true;
};

function () {
	return false;
};

function ($bool) {
	return $bool;
};

function ($bool) {
	return !$bool;
};

function ($array) {
	return count($array) > 0;
};

function ($array) {
	return count($array) <= 0;
};

function () {
	if (doSomething()) {
		return doSomethingElse();
	}
};
