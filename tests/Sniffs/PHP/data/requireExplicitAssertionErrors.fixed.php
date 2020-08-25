<?php

$a = 0;
\assert(\is_int($a));

$b = null;
\assert(\is_int($b) || \is_float($b) || \is_string($b) || \is_bool($b) || $b === null);

$c = [];
\assert(\is_array($c));

$d = [];
\assert(\is_array($d) || \is_iterable($d));

$e = getCallback();
\assert(\is_callable($e));

class Fgh
{

	public function __construct()
	{
		$f = $this;
		\assert($f instanceof $this);

		$g = $this;
		\assert($g instanceof self);
	}

}

$h = fopen('file.txt', 'r');
\assert(\is_resource($h));

$i = new \stdClass();
\assert(\is_object($i));

$j = 0;
\assert(\is_int($j) || \is_float($j));

$k = 'string';
\assert(\is_int($k) || \is_float($k) || \is_bool($k) || \is_string($k));

$l = new \stdClass();
\assert($l instanceof \stdClass);

foreach ([] as $m) {
	\assert(\is_int($m) || \is_float($m) || \is_bool($m));
}

while ($n = next($array)) {
	\assert(\is_int($n));
}

list($o, $p) = [];
\assert(\is_int($o));
\assert(\is_bool($p));

list($q, $r) = [];
\assert(\is_string($q));
\assert($r instanceof \stdClass);

[
	$s,
	$t
] = [];
\assert(\is_int($s));
\assert(\is_string($t));

$u = new ArrayObject();
\assert($u instanceof \Traversable && $u instanceof \Countable);

foreach ([] as $v) {
	\assert(\is_int($v) || \is_float($v) || \is_bool($v));
}

while ($w = next($array)) {
	\assert(\is_int($w));
}

function () {
	list($x, $x2) = [];
	\assert(\is_int($x));
	\assert(\is_float($x2));
};

function () {
	[$y, $y2] = [];
	\assert($y === false);
	\assert($y2 === true);
};

function () {
	$form = $this->formCreator->create(['inputs' => array_map(static function (Config $config): array {
		return $config->toArray();
	}, $this->config->inputs)]);
	\assert($form instanceof Form);
};

$z = new ArrayObject();
\assert($z instanceof \Traversable && $z instanceof \Countable);
