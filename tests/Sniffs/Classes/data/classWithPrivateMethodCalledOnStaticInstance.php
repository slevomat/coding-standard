<?php

class ClassWithPrivateMethodCalledOnSelfInstance
{

	public static function create()
	{
		$self = new static();
		$self->setUp();

		return $self;
	}

	private function setUp()
	{
	}

	public static function foo()
	{
		return new static();
	}

}
