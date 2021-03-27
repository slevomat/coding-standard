<?php // lint >= 8.0

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


class PropertyPromotion
{

	public function __construct(
		public $a,
		string $b,
		private int $c,
	) {

	}

}

/**
 * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
 */
function uselessSuppress($a)
{
	return $a;
}
