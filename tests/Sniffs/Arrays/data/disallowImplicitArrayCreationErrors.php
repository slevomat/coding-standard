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
