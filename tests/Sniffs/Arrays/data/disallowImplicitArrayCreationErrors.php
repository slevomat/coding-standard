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

function differentScope2()
{
	(function () {
		$a = [];
	})();

	$a[] = 'a';
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

function undefinedVariable()
{
	$b = $a ? true : false;
	$a[] = 2;
}

function notImportedViaGlobalStatement()
{
	static $something, $value, $somethingElse;
	$value[] = 'a';
}
