<?php

class ClassWithSomeUnusedProperties extends \Consistence\ObjectPrototype
{

	public $publicProperty;

	protected $protectedProperty;

	private $usedProperty;

	/**
	 * @set(visibility="private")
	 */
	private $sentrySetProperty;

	/**
	 * @get
	 */
	private $sentryGetProperty;

	private $sentryPropertyValue;

	private $sentryPropertyTimestamp;

	private $unusedProperty;

	private $unusedPropertyWhichNameIsAlsoAFunction;

	private $writeOnlyProperty;

	/** @ORM\Column(name="foo") */
	private $doctrineProperty;

	public function foo()
	{
		$this->usedProperty->foo();
		$this->writeOnlyProperty = 'foo';
		$this->unusedPropertyWhichNameIsAlsoAFunction();
		$this->usedPrivateMethod();
		$foo = <<<CODE
{$this->usedPrivateMethodInHereDoc()}
CODE;
	}

	private function usedPrivateMethod()
	{

	}

	private function unusedPrivateMethod()
	{

	}

	public function publicMethod()
	{

	}

	public static function staticPublicMethod()
	{
		self::usedStaticPrivateMethod();
	}

	private static function usedStaticPrivateMethod()
	{

	}

	private static function unusedStaticPrivateMethod()
	{

	}

	private function __construct()
	{

	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedProperty
	 */
	private $unusedPropertyWithSuppress;

	/**
	 * @phpcsSuppress SlevomatCodingStandard.Classes.UnusedPrivateElements.WriteOnlyProperty
	 */
	private $writeOnlyPropertyWithSuppress;

	/**
	 * @phpcsSuppress SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedMethod
	 */
	private function unusedMethodWithSuppress()
	{
		$this->writeOnlyPropertyWithSuppress = true;
	}

	private function &unusedMethodReturningReference()
	{
		return [];
	}

	private$unusedPropertyWithWeirdDefinition;

	private$unusedWriteOnlyPropertyWithWeirdDefinition;

	public function unusedWriteOnlyPropertyWithWeirdDefinitionMethod()
	{
		$this->unusedWriteOnlyPropertyWithWeirdDefinition = null;
	}

	private function usedPrivateMethodWithIncorrectCase()
	{

	}

	public function methodCalledUsedPrivateMethodWithIncorrectCase()
	{
		$this->usedprivatemethodwithincorrectcase();
	}

	private static $unusedStaticProperty1;
	static private $unusedStaticProperty2;

	private $propertyUsedAsArrayKey;

	public function propertyUsedAsArrayKey()
	{
		return [$this->propertyUsedAsArrayKey => true];
	}

	private function usedPrivateMethodInHereDoc()
	{

	}

}
