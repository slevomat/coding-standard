<?php

function () {
	if (false) {
		return false;
	}

	return true;
};

function () {
	if (false) {
		return;
	}

	// Something
};

foreach ($items as $item) {
	if ($item === null) {
		continue;
	}

	// Something
}

while (true) {
	if (false) {
		break;
	}

	// Something
}

do {
	if (false) {
		throw new Exception();
	}

	// Something
} while (true);

function () {
	if (false) {
		yield [];
	}

	// Something
};

function () {
	if (false) {
		exit;
	}

	// Something
};

function () {
	if (false) {
		die;
	}

	// Something
};

function noExitCode() {
	if (true) {
		$variable = 'a';
	} else {
		$variable = 'b';
	}
};

function ifWithElseIf() {
	if (true) {
		// Something
	} elseif (false) {
		// Something else
	} else {
		throw new Exception();
	}
};

function ifHasExitCodeToo() {
	if (true) {
		return true;
	} else {
		return false;
	}
};

function exitCodeIsNotOnFirstLineOfScope() {
	if (true) {
		doSomething();
		return true;
	} else {
		doSomethingElse();
		return false;
	}
}

function noSemicolonInElseScope() {
	if (true) {
		doSomething();
	} else {
		if (true) {

		}
	}
}
