<?php

function () {
	if (true) {
		return null;
	}
};

function () {
	if (true) {
		doSomething();
		return true;
	}
};

function () {
	if (true) {
		return true;
	}

	doSomething();
};

function () {
	if (true) {
		return true;
	}

	return 0;
};

function () {
	if (true) {
		return true;
	} else {
		doSomething();
	}
};

function () {
	if (true) {
		return true;
	} else {
		return 'string';
	}
};

function () {
	if (true) {
		doSomething();
	} else {
		return false;
	}
};

function () {
	if (true) {
		return 0.0;
	} else {
		return false;
	}
};

function () {
	if (true) {
		return true || false;
	} else {
		return false;
	}
};

function () {
	if (true) {
		return false;
	}

	return true || false;
};
