<?php

function () {
	if (true || false) {

	}
};

function () {
	if (
		doSomething('a')
		&& doSomething('b')
		&& (
			doAnything()
			|| doNothing()
			|| (
				doWhatever()
				&& justDo()
			)
		)
		&& doSomething('c')
	) {
	}
};
