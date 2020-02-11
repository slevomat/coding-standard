<?php

function ($values) {
	foreach ($values as $value) {
		if ($value === null) {
			return;
		} elseif ($value) {
			doSomething();
		}
	}
};

function ($values) {
	foreach ($values as $value) {
		if ($value === null) {
			return;
		} elseif ($value) {
			doSomething();
			doMore();
		}
	}
};
