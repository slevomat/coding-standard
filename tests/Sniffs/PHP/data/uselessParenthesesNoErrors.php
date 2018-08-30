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

new self($a);
new static($b);
new parent($c);

$z = ($a ? '0' : '1') ? '2' : '3';
$zz = ($a + $b);
$zzz = !($a) ? true : false;

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
