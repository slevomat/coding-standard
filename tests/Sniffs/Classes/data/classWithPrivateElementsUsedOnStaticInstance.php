<?php

class ClassWithPrivateElementsUsedOnSelfInstance
{

	private const CONSTANT = 'constant';

	private $property = 'property';

	private static $staticProperty1 = 'staticProperty1';
	static private $staticProperty2 = 'staticProperty2';

	public static function create()
	{
		$self = new static();
		$self->setUp();
		$self->property;

		$self::staticSetUp();
		$self::CONSTANT;
		$self::$staticProperty1;
		$self::$staticProperty2;

		return $self;
	}

	private function setUp()
	{
	}

	private static function staticSetUp()
	{
	}

	public static function foo()
	{
		return new static();
	}

}
