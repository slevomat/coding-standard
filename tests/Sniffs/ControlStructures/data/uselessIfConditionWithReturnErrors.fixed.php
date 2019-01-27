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
	if (doSomething()) {
		return doSomethingElse();
	}
};
