<?php

/**
 * @param int $a
 */
function (int $a) {

};

/** @see Whatever */
$b = 0;

/** @var int */
$c = 0;

/** @var int|int[] $d */
$d = 0;

/** @var int[] $e */
$e = [];

/** @var int $f */
$f += 0;

/** @var mixed $g */
$g = getMixed();

/** @var int $differentVariable */
$h = null;

/** @var string $differentVariable */
foreach ([] as $i) {
}

/** @var bool $differentVariable */
while ($j = next($array)) {
}

/** @var float $k */
while ($whatever = getSomething($k)) {
}

/** @var int $differentVariable */
list($j) = [];

/** @var int $differentVariable */
[$k] = [];

/** @var bool $l */
[$l];

/** @var $m invalid annotation */
$m = 0;
