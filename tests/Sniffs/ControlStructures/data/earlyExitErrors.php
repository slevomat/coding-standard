<?php

function () {
	if (true) {
		// Something
	} else {
		return false;
	}
};

// Identical condition
function () {
	if ($bool === true) {
		// Something
	} else {
		return;
	}
};

// Not identical condition
foreach ($items as $item) {
	if ($item !== null) {
		// Something
	} else {
		continue;
	}
}

// Equal condition
while (true) {
	if ($string == '') {
		// Something
	} else {
		break;
	}
}

// Not equal condition
do {
	if ($string != '') {
		// Something
	} else {
		throw new Exception();
	}
} while (true);

function greateThanOrEqualCondition() {
	if ($number >= 0) {
		// Something
	} else {
		yield [];
	}
}

function greateThanCondition() {
	if ($number > 0) {
		// Something
	} else {
		exit;
	}
}

function lessThanOrEqualCondition() {
	if ($number <= 0) {
		// Something
	} else {
		die;
	}
}

function lessThanCondition() {
	if ($number < 0) {
		// Something
	} else {
		return;
	}
}

function simpleCondition($password) {
	if ($password->isValid()) {
		// Something
	} else {
		return false;
	}
}

function negativeCondition($token) {
	if (!$token->isExpired()) {
		// Something
	} else {
		return false;
	}
}

function instanceOfCondition($e) {
	if ($e instanceof Exception) {
		logError($e);
	} else {
		return;
	}
}

function noSemicolonInIfScope() {
	if (true) {
		if (false) {
			// Something
		}
	} else {
		return;
	}
}

function ifAtTheEndOfFunction() {
	$result = doSomething();
	if ($result) {
		doMore();
	}
}

while (true) {
	$result = doSomething();
	if ($result) {
		doMore();
	}
}

do {
	$result = doSomething();
	if ($result) {
		doMore();
	}
} while (true);

foreach ($items as $item) {
	if ($item !== null) {
		doSomething();
	}
}

for ($i = 0; $i < 100; $i++) {
	if ($i % 2 === 0) {
		doSomething();
	}
}
