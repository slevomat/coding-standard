<?php

class Whatever
{

	public function equalForParameterDefaultValue($parameter = null)
	{

	}

	public function simpleAssigment()
	{
		$variable = null;
	}

	public function notOperator($a, $b)
	{
		$variable = $a || $b;
	}

	public function differentVariables($b, $c)
	{
		$a = $b + $c;
	}

	public function noVariableAfterAssigment($b, $c)
	{
		$a = ($b * $c);
	}
}
