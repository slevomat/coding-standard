<?php

function ($values) {
	foreach ($values as $value) {
		if ($value === null) {
			return;
		}

		if ($value) {
			doSomething();
		}
	}
};

function ($values) {
	foreach ($values as $value) {
		if ($value === null) {
			return;
		}

		if (!$value) {
			continue;
		}

		doSomething();
		doMore();
	}
};
