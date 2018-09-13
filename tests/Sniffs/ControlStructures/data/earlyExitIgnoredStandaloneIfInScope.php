<?php

function () {
	if (true) {
		doSomething();
	}
};

function ($values) {
	foreach ($values as $value) {
		if ($value) {
			doSomething();
		}
	}
};
