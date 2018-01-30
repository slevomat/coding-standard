<?php

function () {
	if (true) {
		return true;
	} else {
		return false;
	}
};

function () {
	if (true) {
		// Something
	} else {
		return;
	}
};

foreach ($items as $item) {
	if ($item !== null) {
		// Something
	} else {
		continue;
	}
}

while (true) {
	if (true) {
		// Something
	} else {
		break;
	}
}

do {
	if (true) {
		// Something
	} else {
		throw new Exception();
	}
} while (true);

function () {
	if (true) {
		// Something
	} else {
		yield [];
	}
};

function () {
	if (true) {
		// Something
	} else {
		exit;
	}
};

function () {
	if (true) {
		// Something
	} else {
		die;
	}
};
