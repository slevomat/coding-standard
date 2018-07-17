<?php

function noParameter()
{

}

/**
 * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
 */
function wholeSniffSupress($a)
{

}

/**
 * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
 */
function codeSupress($a)
{

}

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

function parameterInString($a)
{
	echo "$a";
}

function parameterInHeredoc($a)
{
	echo <<<TEXT
	$a
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

abstract class Whatever
{

	public abstract function doSomething($a);

	public function setProperty(string $property, $value): void
	{
		$this->$property = $value;
	}

}

