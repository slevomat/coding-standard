<?php

function () use ($boo, $foo, $doo) {
	echo $foo;
	echo $doo;
};

function () use (
	$boo,
	$foo,
	$doo
) {
	echo $boo;
	echo $doo;
};

function () use ($boo, $foo, $doo) {
	echo $boo;
	echo $foo;
};

function () use (
	$boo
) {
	(function () {
		$boo = 'boo2';
	})();
};

function ($arrays) use ($boo) {
	foreach ($arrays as $key => $array) {
		$value = array_filter($array, function ($boo) {
			return $boo === 'boo';
		});
	}
};

(function ($type, $buffer) use (&$output, &$successful) : void {
	$successful = false;
})();
