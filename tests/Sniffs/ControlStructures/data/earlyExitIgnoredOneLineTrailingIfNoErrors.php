<?php

function ($values) {
	foreach ($values as $value) {
		if ($value) {
			doSomething();
		}
	}
};

function ($values) {
	foreach ($values as $value) {
		$value .= 'whatever';

		if ($value) {
			doSomething();
		}
	}
};
