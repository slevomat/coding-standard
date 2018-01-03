<?php

namespace Foo;

class ClassWithPrivateElementsUsedOnSameClass
{

	private const CONSTANT = 'constant';

	private $property = 'property';

	private static $staticProperty1 = 'staticProperty1';
	static private $staticProperty2 = 'staticProperty2';

	public static function create()
	{
		$self = new ClassWithPrivateElementsUsedOnSameClass();
		$self->setUp();
		$self->property;

		$self::staticSetUp();
		\Foo\ClassWithPrivateElementsUsedOnSameClass::CONSTANT;
		$self::$staticProperty1;
		ClassWithPrivateElementsUsedOnSameClass::$staticProperty2;

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
