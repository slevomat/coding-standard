<?php

function something(
	$a,
	$b
) {

}

class Whatever
{

	function __construct(
		$a
	) {

	}

}

function (
	$a,
	int $b = 0,
	$c = []
) {
	return fn (
		$a,
		$b
	) => true;
};
