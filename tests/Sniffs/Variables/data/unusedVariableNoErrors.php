#!/usr/bin/env php
<?php
$used = true;

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
	global $bar;
	$bar = new FooBar();
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
	echo "${a}";
};

function () {
	$a = '';
	echo "${a[0]}";
};

function () {
	$a = '';
	echo "$a()";
};

function () {
	$a = '';
	echo <<<TEXT
	$a
TEXT;
};

function () {
	$a = '';
	echo <<<TEXT
	${a}
TEXT;
};

function () {
	$a = 10;
	max(1, $a += 10);
};

function () {
	$a = 1;
	$b = 1;
	$c = [
		$b-- => $a++,
		--$b => ++$a,
	];
	return $c;
};

class Whatever
{

	public function listFunction($a, $b) {
		list($this->a, $this->b) = [$a, $b];
	}

}

function () {
	$i = 0;
	while ($i++ <= 10) {
	}
};

function () {
	$i = 0;
	do {
	} while (++$i <= 10);
};

function () {
	$i = 10;
	while ($i-- > 0) {
	}
};

function () {
	$i = 10;
	do {
	} while (--$i > 0);
};

function ($data) {
	$i = 0;
	$c = '';
	foreach ($data as $c) {
		$c = $i++;
	}
	echo $c;
};

function ($values) {
	$expectedKey = 0;

	foreach ($values as $key => $value) {
		if ($key !== $expectedKey++) {
			return $value;
		}
	}

	return null;
};

function () {
	$foo = 'ok';
	$bar = "\\$foo";

	echo $bar;
};

function ($values) {
	$expectedKey = 0;

	foreach ($values as $key => $value) {
		if ($key !== ($expectedKey += 1)) {
			return $value;
		}
	}

	return null;
};

function () {
	$x =& getReference();
	$x = '';
};

function ($values) {
	foreach ($values as &$value) {
	   $value = 'changed';
	}
};

function () {
	$bool = false;
	for ($i = 0; $i < 10; $i++) {
		if (!$bool) {
			$bool = true;
		}
	}
};

function () {
	$x = 1;
	foreach ([2, 4, 8] as $y) {
		print "$x => $y\n";
		$x++;
	}
};

function () {
	$runaway = 6; // Max depth is 5
	while(($runaway-- > 0)) {
		echo ".";
	}
};

function () {
	$a = [];
	$i = 0;

	foreach ([1, 2, 3] as $x) {
		$a[$i++] = $x;
	}

	return $a;
};

function ($result) {
	if ($row = mysqli_fetch_assoc($result)) {
		do {
			doSomething($row);
		} while ($row = mysqli_fetch_assoc($result));
	}
};

function ($result) {
	if (true) {

	}

	while ($row = current($result)) {
		doSomething($row);
	}
};

function () {
	$GLOBALS = [];
	$_SERVER = [];
	$_GET = [];
	$_POST = [];
	$_FILES = [];
	$_COOKIE = [];
	$_SESSION = [];
	$_REQUEST = [];
	$_ENV = [];
};


function () {
	$a = 1;
	return get_defined_vars();
};

function ($c) {
	$a = 0;

	$b = $c + $a--;

	return $b;
};

function ($c) {
	$a = 0;

	$b = $c + --$a;

	return $b;
};

function ($i) {
	$result = [];
	foreach ([] as $key => $item) {
		$result[$key ?: $i++] = $item;
	}
	return $result;
};
