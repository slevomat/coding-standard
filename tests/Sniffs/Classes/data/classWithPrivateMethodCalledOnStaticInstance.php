<?php

class ClassWithPrivateMethodCalledOnSelfInstance
{

	public static function create()
	{
		$self = new self();
		$self->setUp();

		return $self;
	}

	private function setUp()
	{
	}

}
