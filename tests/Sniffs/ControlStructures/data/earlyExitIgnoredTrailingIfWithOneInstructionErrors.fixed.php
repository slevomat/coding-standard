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

function ($values) {
	foreach ($values as $value) {
		if (!$value) {
			continue;
		}

		if (true) {
			doSomething();
		}
	}
};

function ($values) {
	foreach ($values as $value) {
		if (!$value) {
			continue;
		}

		foreach ([] as $item) {
			doSomething();
		}
	}
};
