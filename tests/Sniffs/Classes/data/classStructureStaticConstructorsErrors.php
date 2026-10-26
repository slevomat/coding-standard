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

	public function method()
	{
	}

	public static function createSecond(): Second
	{
	}

}

class Third
{

}
