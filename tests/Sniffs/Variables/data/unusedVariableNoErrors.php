<?php

class NoError
{
	private $noError = 'noError';

	private static $static;

	public function __construct($parameter)
	{
		self::$static = 'static';
		$this->$parameter = 'this';
	}

}

$noErrorToo = null;
function ($noError = 'noError') use ($noErrorToo) {

};

$used = true;

function foo($foo) {
	return preg_replace_callback('~~', function ($matches) {
		return $matches[0];
	}, $foo);
}

echo $used;

function ($values) {
	$foo = '';

	foreach ($values as $value) {
		echo $foo . $value;
	}
};

function () {
	for ($i = 0; $i < 10; $i++) {
	}
};

function ($values) {
	foreach ($values as $value) {
		$foo = 'foo' . $value;
	}
	echo $foo;
};

function ($values) {
	list($a, $b) = $values;
	return $a + $b;
};

function ($values) {
	[$c, $d] = $values;
	return $c * $d;
};

function ($values) {
	$current = 'current';
	$next = 'next';

	while ($next) {
		if ($current) {

		}

		$current = false;

		if (true) {
			foreach ($values as $value) {
				$next = $value;
			}
		}

		do {
			$previous = 'previous';
		} while ($previous);
	}
};

function (&$parameter) {
	$parameter = 'value-by-reference';
};

function () use (&$inheritedVariable) {
	$inheritedVariable = 'value-by-reference';
};

function ($interval) {
	$j = 0;
	for ($i = $j; $i < 10; $i += $interval) {
	}
};

function () {
	static $static = false;
	if ($static) {
		return;
	}

	$static = true;
};

function () {
    $a = 'a';
    $b = 'b';

    $this->compact;

	return compact('a', "b");
};

function () {
	$a = '';
	echo "$a";
};

function () {
	$a = '';
	echo <<<TEXT
	$a
TEXT;
};
