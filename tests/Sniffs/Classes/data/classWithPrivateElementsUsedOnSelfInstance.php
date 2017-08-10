<?php // lint >= 7.1

class ClassWithPrivateElementsUsedOnSelfInstance
{

	private const CONSTANT = 'constant';

	private $property = 'property';

	private static $staticProperty = 'staticProperty';

	public static function create()
	{
		$self = new self();
		$self->setUp();
		$self->property;

		$self::staticSetUp();
		$self::CONSTANT;
		$self::$staticProperty;

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
		return new self();
	}

}
