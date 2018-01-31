<?php

function () {
	if (!true) {
		return false;
	}

	// Something
};

// Identical condition
function () {
	if ($bool !== true) {
		return;
	}

	// Something
};

// Not identical condition
foreach ($items as $item) {
	if ($item === null) {
		continue;
	}

	// Something
}

// Equal condition
while (true) {
	if ($string != '') {
		break;
	}

	// Something
}

// Not equal condition
do {
	if ($string == '') {
		throw new Exception();
	}

	// Something
} while (true);

function greateThanOrEqualCondition() {
	if ($number < 0) {
		yield [];
	}

	// Something
};

function greateThanCondition() {
	if ($number <= 0) {
		exit;
	}

	// Something
};

function lessThanOrEqualCondition() {
	if ($number > 0) {
		die;
	}

	// Something
};

function lessThanCondition() {
	if ($number >= 0) {
		return;
	}

	// Something
};

function simpleCondition($password) {
	if (!$password->isValid()) {
		return false;
	}

	// Something
};

function negativeCondition($token) {
	if ($token->isExpired()) {
		return false;
	}

	// Something
};

function instanceOfCondition($e) {
	if (!($e instanceof Exception)) {
		return;
	}

	logError($e);
};

function noSemicolonInIfScope() {
	if (!true) {
		return;
	}

	if (false) {
		// Something
	}
}
