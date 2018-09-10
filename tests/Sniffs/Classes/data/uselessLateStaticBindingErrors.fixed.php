<?php

final class Whatever
{

	public function getClassName(): string
	{
		return self::class;
	}

	public function getSomething()
	{
		return self::$something;
	}

	public function callSomething()
	{
		self::something();
	}

}
