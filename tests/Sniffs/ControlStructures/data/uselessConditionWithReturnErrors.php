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
	}

	return false;
};

function ($e, $number) {
	if (true || $e instanceof Exception || $number <= 0) {
		return false;
	} else {
		return true;
	}
};

function () {
	return true ? true : false;
};

function () {
	return false ? true : false;
};

function ($bool) {
	return $bool ? true : false;
};

function ($bool) {
	return $bool ? false : true;
};

function ($array) {
	return count($array) > 0 ? true : false;
};

function ($array) {
	return count($array) > 0 ? false : true;
};

function () {
	if (doSomething()) {
		if (doSomethingElse()) {
			return true;
		}

		return false;
	}
};
