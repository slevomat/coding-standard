<?php // lint >= 7.4

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

$a = $b / (100 + $c);
$a = (100 - $b) * $c;
$a = ('100' . '000') * $c;

$a = 'a' . '/' . (32 - ($b * 8));

$var = $foo && ($bar > 0 || $baz > 0) ? true : false;

include($file);
include_once($file);
require($file);
require_once($file);

(require 'file.php')();

if ($a
	&& (null === $b || null === $c)) {
}

$a = ($b * $c) - $d;
$a = ($b * 60 * 60) + ($c * 60) + $d;

$c = (clone $a->foo())->bar($b);

$d = (10 - ($x % 10)) % 10;
$e = (($x % 10) - 10) % 10;

$x = (object) ([
    'a',
] + $defaults('b'));

function () {
	$y = (yield from whatever()) ?? $y;
};

$x = ($y - $yy) ** $z;

$hue = 60 * (($blueFraction - $redFraction) / $delta + 2);

if ($hit === true || (is_array($hit) && count(array_filter($hit)) >= count($keys) * 0.5)) {
}

$hitRatio = (int) round($hitCount / ($loadCount / 100 ?: 1), 1);

$a = 10 + 1 - (5 + 4);

$a = 10 / (5 / 2);

$a = 10 / (5 * 2);

pow(10, -($x + 1));

if (! ($i === $j - 1)) {
}

function ($value) {
	return ($value & $value - 1) === 0;
};

$x = fn ($a, $b) => $a . $b;

// Must be last
return true
    ? 100
    : (int) ((100 <=> 50) * 100);

