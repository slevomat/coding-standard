<?php

use NoError;

class Foo {
	use NoError;
}

$boo = 'boo';

function () use ($boo, $foo) {
	echo (function () use ($foo) {
		return $foo;
	})();
	echo $boo;
};

function ($values) use ($doo): ?string {
	foreach ($values as $key => $value) {
		if ($value === 'boo') {
			return $doo;
		}
	}

	return null;
};

function () use ($eoo) {
	echo "$eoo";
};

function () use ($foo) {
	echo <<<TEXT
	${foo}
TEXT;
};
