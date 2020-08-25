<?php

/** @var int $a */
$a = 0;

/** @var int|float|string|bool|null $b */
$b = null;

/** @var array $c */
$c = [];

/** @var array|iterable $d */
$d = [];

/** @var callable $e */
$e = getCallback();

class Fgh
{

	public function __construct()
	{
		/** @var $this $f */
		$f = $this;

		/** @var self $g */
		$g = $this;
	}

}

/** @var resource $h */
$h = fopen('file.txt', 'r');

/** @var object $i */
$i = new \stdClass();

/** @var int|numeric $j */
$j = 0;

/** @var scalar $k */
$k = 'string';

/** @var \stdClass $l */
$l = new \stdClass();

/** @var numeric|bool $m */
foreach ([] as $m) {
}

/** @var int $n */
while ($n = next($array)) {
}

/**
 * @var int $o
 * @var bool $p
 */
list($o, $p) = [];

/** @var string $q */
/** @var \stdClass $r */
list($q, $r) = [];

/**
 * @var int $s
 * @var string $t
 */
[
	$s,
	$t
] = [];

/** @var \Traversable&\Countable $u */
$u = new ArrayObject();

foreach ([] as $v) {
	/** @var numeric|bool $v */
}

while ($w = next($array)) {
	/** @var int $w */
}

function () {
	list($x, $x2) = [];
	/**
	 * @var int $x
	 * @var float $x2
	 */
};

function () {
	[$y, $y2] = [];
	/**
	 * @var false $y
	 * @var true $y2
	 */
};

function () {
	/** @var Form $form */
	$form = $this->formCreator->create(['inputs' => array_map(static function (Config $config): array {
		return $config->toArray();
	}, $this->config->inputs)]);
};

$z = new ArrayObject();
/** @var \Traversable&\Countable $z */
