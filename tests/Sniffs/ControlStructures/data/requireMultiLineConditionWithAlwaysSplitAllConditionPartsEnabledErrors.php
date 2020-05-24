<?php

function () {
	if (doSomething('a') && doSomething('b')
		&& (
			doAnything()
			|| doNothing() || (doWhatever() && justDo()))
		&& doSomething('c')
	) {
	}
};
