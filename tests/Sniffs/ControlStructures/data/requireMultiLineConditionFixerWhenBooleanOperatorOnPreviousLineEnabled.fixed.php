<?php

function () {
	if (
		doSomething('veryyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyy long') &&
		true
	) {

	} elseif (
		doSomething('veryyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyy long') &&
		true
	) {

	}

	while (
		doSomething('veryyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyy long') &&
		true
	) {

	}

	do {

	} while (
		doSomething('veryyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyy long') &&
		true
	);

	if (
		doSomething('a') &&
		doSomething('b') &&
		(
			doAnything() ||
			doNothing() ||
			(
				doWhatever() &&
				justDo()
			)
		) &&
		doSomething('c')
	) {

	}
};
