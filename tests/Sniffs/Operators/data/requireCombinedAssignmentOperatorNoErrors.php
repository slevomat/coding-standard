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
		$a = $b + $c;
	}

	public function noVariableAfterAssignment($b, $c)
	{
		$a = ($b * $c);
	}

	public function shortList($array)
	{
		[$a, $b, $c, $d, $e] = $array + range(0, 4);
	}

	public function moreOperators($a, $b, $c)
	{
		$a = $a / $b / $c;
	}

	public function probablyStringOffsets($a, $b, $c)
	{
		$a[0] = $a[0] & '';
		$b[0] = $b[0] | '';

		$a[0] = $a[0] & "";
		$b[0] = $b[0] | "";

		$a[0] = $a[0] & "{$c}";
		$b[0] = $b[0] | "{$c}";

		$a[0] = $a[0] & <<<CODE
			Something
CODE;
		$b[0] = $b[0] | <<<CODE
			Something
CODE;
	}

}

