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
		$this['a'] = 'a';
	}

}

function noAssignment()
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

function createByFunctionCall()
{
	$a = array_map(function () {
	}, []);
	$a[] = 'a';
}

function globalArrays()
{
	$_SERVER['x'] = 'x';
}

function createdInForeach($values)
{
	foreach ($values as $value) {
		$value[] = true;
	}
}

function createdByList($values)
{
	list($value) = $values;
	$value[] = 'a';
}

function createdByShortList($values)
{
	[$value] = $values;
	$value[] = 'a';
}

function createdByReferencedParameterInFunctionCall($query)
{
	parse_str($query, $arguments);
	$arguments[] = 'a';
}

function staticVariable()
{
	static $value;
	$value[] = true;
}

function importedViaGlobalStatement()
{
	global $something, $value, $somethingElse;
	$value[] = 'a';
}
