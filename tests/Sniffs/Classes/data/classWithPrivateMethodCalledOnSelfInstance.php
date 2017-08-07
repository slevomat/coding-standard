<?php

class ClassWithPrivateMethodCalledOnSelfInstance
{

	public static function create()
	{
		$self = new self();
		$self->setUp();

		self::bar();

		return $self;
	}

	private function setUp()
	{
	}

	public static function foo()
	{
		return new self();
	}

	private static function bar()
	{
	}

}
