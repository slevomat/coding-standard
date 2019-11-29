<?php // lint >= 7.4

function noParameter()
{

}

/**
 * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
 */
function wholeSniffSuppress($a)
{

}

/**
 * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
 */
function codeSuppress($a)
{

}

/**
 * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
 */
function ($closureSuppress)
{

};

function usedParameter($a)
{
	echo $a;
}

function usedParameterInAnotherScope($a)
{
	if (true) {
		echo $a;
	}
}

function parameterInString($a, $b)
{
	echo "$a";
	echo "${b}";
}

function parameterInHeredoc($a, $b)
{
	echo <<<TEXT
	$a ${b}
TEXT;
}

function parameterInCompactFunction($a)
{
	return compact('a');
}


function parameterInParenthesis($a) {
	return ($a);
}


function compactIsNotFunction($a, $b) {
	$b->compact;

	return compact('a');
}

fn ($a, $b) => $a + $b;

abstract class Whatever
{

	public abstract function doSomething($a);

	public function setProperty(string $property, $value): void
	{
		$this->$property = $value;
	}

	private static function staticMethodCallViaVariable(string $methodName): ?self
	{
		return self::$methodName();
	}

}

