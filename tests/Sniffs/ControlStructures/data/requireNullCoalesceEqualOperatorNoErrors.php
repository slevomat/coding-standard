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

