<?php

class Whatever
{

	public function noVariable()
	{
		$this->noVariable = false;
		self::$noVariable = false;
	}

}

$noSecondVariable = true;

$variable = $variable[0];

$variable = $differentVariable = true;
