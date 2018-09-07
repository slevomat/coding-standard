<?php

$a[] = 'a';

function ()
{
	$a[] = 'a';
};

function invalidExplicitCreation()
{
	$a = true;
	$a[] = 'a';
}

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
