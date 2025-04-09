<?php

class Whatever
{

	public function equalForParameterDefaultValue($parameter = null)
	{

	}

	public function simpleAssignment()
	{
		$variable = null;
	}

	public function notOperator($a, $b)
	{
		$variable = $a || $b;
	}

	public function differentVariables($b, $c)
	{
		$a = $b ?? $c;
	}

	public function noVariableAfterAssignment($b, $c)
	{
		$a = ($b ?? $c);
	}

	public function shortList($array)
	{
		[$a, $b, $c, $d, $e] = $array ?? range(0, 4);
	}

	public function moreOperators($a, $b, $c)
	{
		$a = $a ?? $b ?? $c;
	}

}

if (false === $a) {
	$a = true;
}

if ($b == null) {
	$b = true;
}

if ($c === false) {
	$c = true;
}

if ($d === null && $dd !== null) {
	$d = true;
}

if ($e === null) {
	[$e] = [];
}

if ($f === null) {
	$ff = true;
}

if ($g === null) {
	$g = true;
	$gg = true;
}

if ($h === null) {
	$h = true;
} else {
	doSomething();
}

if ($i === null) {
	$i = true;
} elseif (false) {
	doSomething();
}

if ($j === null) {
	doSomething();
	$j = true;
}

if ($k === null) $k = true;
