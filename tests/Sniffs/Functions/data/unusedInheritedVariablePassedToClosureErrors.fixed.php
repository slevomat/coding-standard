<?php

function () use ($foo, $doo) {
	echo $foo;
	echo $doo;
};

function () use (
	$boo,
	$doo
) {
	echo $boo;
	echo $doo;
};

function () use ($boo, $foo) {
	echo $boo;
	echo $foo;
};

function ()  {
	(function () {
		$boo = 'boo2';
	})();
};

function ($arrays)  {
	foreach ($arrays as $key => $array) {
		$value = array_filter($array, function ($boo) {
			return $boo === 'boo';
		});
	}
};

(function ($type, $buffer) use (&$successful) : void {
	$successful = false;
})();
