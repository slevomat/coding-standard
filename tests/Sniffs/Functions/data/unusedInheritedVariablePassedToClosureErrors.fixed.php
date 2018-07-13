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

