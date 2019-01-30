<?php

function ($bool) {
	return $bool;
};

function () {
	if (doSomething()) {
		return doSomethingElse();
	}
};
