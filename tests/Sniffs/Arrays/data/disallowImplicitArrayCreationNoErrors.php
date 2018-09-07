<?php

function explicitCreation()
{
	$a = [];
	$a[] = 'a';
}

class Whatever
{

	function noVariable()
	{
		$this->a[] = 'a';
		self::$a[] = 'a';
	}

}

function noAssigment()
{
	$a[]++;
}

function parameter($c, $b, $a)
{
	$a[] = 'a';
}

function () use ($a)
{
	$a[] = 'a';
};

function inCondition()
{
	$a = [];

	if (true) {
		$a[] = 'a';
	}
}

function longArray()
{
	$a = array();
	$a[] = 'a';
}
