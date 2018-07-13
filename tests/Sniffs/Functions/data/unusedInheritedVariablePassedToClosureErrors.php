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

