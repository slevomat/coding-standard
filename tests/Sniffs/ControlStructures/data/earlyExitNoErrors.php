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
}

function ifWithElseIf() {
	if (true) {
		// Something
	} elseif (false) {
		// Something else
	} else {
		throw new Exception();
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

function () {
	if (true) {
		doSometimesSomething();
	}

	doSomething();
};

while (true) {
	$result = doSomething();
	if ($result) {
		doMore();
	}

	doSomethingAgain();
}

do {
	$result = doSomething();
	if ($result) {
		doMore();
	}

	doSomethingAgain();
} while (true);

foreach ($items as $item) {
	if ($item !== null) {
		doSomething();
	}

	doSomethingAgain();
}

for ($i = 0; $i < 100; $i++) {
	if ($i % 2 === 0) {
		doSomething();
	}

	doSomethingAgain();
}

function passWhenConditionIsOnTheEndOfMethod()
{
	for ($i = 0; $i < 100; $i++) {
		if ($i % 2 === 0) {
			doSomething();
		}
	}
}
