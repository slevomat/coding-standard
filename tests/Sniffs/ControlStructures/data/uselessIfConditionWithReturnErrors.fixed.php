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
	if ($bool) {
		return true;
	} else {
		return false;
	}
};

function ($e, $number) {
	return false && !($e instanceof Exception) && $number > 0;
};

function () {
	if (doSomething()) {
		if (doSomethingElse()) {
			return true;
		}

		return false;
	}
};

function () {
	return (
		!empty($day['from']) || empty($day['to'])
	) && (
		empty($day['from']) || !empty($day['to'])
	);
};
