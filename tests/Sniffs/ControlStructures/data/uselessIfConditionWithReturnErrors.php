<?php

function () {
	if (true) {
		return true;
	} else {
		return false;
	}
};

function () {
	if (false) {
		return true;
	} else {
		return false;
	}
};

function () {
	if (true) {
		return false;
	} else {
		return true;
	}
};

function () {
	if (false) {
		return false;
	} else {
		return true;
	}
};

function () {
	if (true) {
		return true;
	}

	return false;
};

function ($bool) {
	if ($bool) {
		return true;
	} else {
		return false;
	}
};

function ($e, $number) {
	if (true || $e instanceof Exception || $number <= 0) {
		return false;
	} else {
		return true;
	}
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
	if ((
		empty($day['from']) && !empty($day['to'])
	) || (
		!empty($day['from']) && empty($day['to'])
	)) {
		return false;
	}

	return true;
};
