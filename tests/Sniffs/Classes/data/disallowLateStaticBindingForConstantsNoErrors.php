<?php

function () {
	static $variable;
};

class Whatever
{

	public function getClassName(): string
	{
		return static::class;
	}

	public function getSomething()
	{
		return static::$something;
	}

	public function callSomething()
	{
		static::something();
	}

}
