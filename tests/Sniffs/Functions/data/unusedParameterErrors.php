<?php

function unusedParameters($a, $b)
{
}

function sameVariableNameInAnotherScope($a)
{
	return function () {
		$a = '';
	};
}

function differentVariableNameInString($a)
{
	echo "$b";
}

function differentVariableNameInHeredoc($a)
{
	echo <<<TEXT
	$b
TEXT;
}

function differentParameterInCompactFunction($a)
{
	return compact('b');
}


class Whatever
{

	public function propertyWithSelf($a)
	{
		self::$a = 'a';
	}

	public function propertyWithStatic($a)
	{
		static::$a = 'a';
	}

}

function ($a) {

};

function sameNameParameterInAnotherScope($a)
{
	return function ($a) {
		return $a;
	};
}
