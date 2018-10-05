<?php

$a[] = 'a';

function ()
{
	$a[] = 'a';
};

function differentVariable()
{
	$b = [];
	$a[] = 'a';
}

function differentScope()
{
	$a = [];
	(function () {
		$a[] = 'a';
	})();
}

function parenthesesBefore()
{
	$x = (10) + $value;
	$value[] = 'a';
}

function variableReset()
{
	$a[] = 'a';
	$a = null;
}
