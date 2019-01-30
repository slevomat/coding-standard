<?php

function ($bool) {
	if ($bool) {
		return true;
	} else {
		return false;
	}
};

function () {
	if (doSomething()) {
		if (doSomethingElse()) {
			return true;
		}

		return false;
	}
};
