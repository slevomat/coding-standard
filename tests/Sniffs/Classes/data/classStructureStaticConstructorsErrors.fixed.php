<?php

class Outer
{

	public static function createOuter(): Outer
	{
	}

	public static function createSelf(): self
	{
	}

}

class Second
{

	public static function createSecond(): Second
	{
	}

	public function method()
	{
	}

}

class Third
{

}
