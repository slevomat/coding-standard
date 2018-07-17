<?php

$unused = '';

function foo($foo) {
	return preg_replace_callback('~[f]+~', function ($matches) {
		$firstMatch = $matches[1];
		return $matches[0];
	}, $foo);
}

$unused = 'unused';

function ($values) {
	$foo = '';

	foreach ($values as $value) {
		$value .= $foo;
	}
};

function ($values) {
	foreach ($values as $key => $value) {
	}
};

function ($values) {
	list($a, $b) = $values;
};

function ($values) {
	[$c, $d] = $values;
};

function () {
	for ($i = 0;;) {

	}
};

function ($values) {
	while ($current = current($values)) {

	}
};

function ($values) {
	do {
	} while ($current = current($values));
};

function () {
	$count = 0;
	$count++;
};

function () use ($a, &$b) {
	$c = 'c';
};

function () {
	$a = 'a';

	echo 'compact';

	return compact('anything');
};

function () {
    $a = 'a';

    return function () {
		return compact('a');
	};
};

function () {
	$unused = false;
};


function () {
	$a = '';
	echo "$b";
};

function () {
	$a = '';
	echo <<<TEXT
	$b
TEXT;
};

