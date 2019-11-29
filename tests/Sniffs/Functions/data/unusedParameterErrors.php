<?php // lint >= 7.4

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

fn ($a) => $b;

function sameNameParameterInAnotherScope($a)
{
	return function ($a) {
		return $a;
	};
}
