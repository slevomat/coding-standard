<?php

function doSomething($parameter) {

}

while ($current) {

}

if ($true) {

}

$a = 10 ? 1 : 0;

$b = $c + $d ? 1 : 0;

$closure = function ($parameter) use ($inheritedVariable) {

};

$closure(10);

max($a, $b);

if (isset($a)) {

}

if (empty($a)) {

}

unset($a);

$class = new class ($number) {
};

(bool) $a;

$b = $array['function']($parameter);

$foo = new Foo();
new self($a);
new static($b);
new parent($c);

$z = ($a ? '0' : '1') ? '2' : '3';
$zz = ($a + $b);
$zzz = !($a) ? true : false;
$zzzz = null !== ($a = 'a') ? true : false;

exit($a);
die($a);

(function ($b): void {
	// Do something
})($b);

eval($c);

list($c) = [];

switch (true) {
	case !($boo !== null):
}

class ClassWithClosure
{
	private $closure;

	public function __construct()
	{
		$this->closure = function () {
			echo 123;
		};

		($this->closure)();
	}
}

$response = (new Response())->withStatus(200);

function () {
	return [
		'a' => ('aa' . 'bb'),
	];
};

$a = $b / (100 + $c);
$a = (100 - $b) * $c;
$a = ('100' . '000') * $c;

$a = $b + (100 - $c);

$a = 'a' . '/' . (32 - ($b * 8));

$var = $foo && ($bar > 0 || $baz > 0) ? true : false;

include($file);
include_once($file);
require($file);
require_once($file);

if ($a
	&& (null === $b || null === $c)) {
}

$a = ($b * $c) - $d;
$a = ($b * 60 * 60) + ($c * 60) + $d;

// Must be last
return true
    ? 100
    : (int) ((100 / 50) * 100);


$c = (clone $a->foo())->bar($b);
