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

	private function methodUsedInHeredoc()
	{

	}

	private $propertyUsedInHeredoc;

	public function methodWithHeredoc()
	{
		$code = <<<CODE
$this->propertyUsedInHeredoc
code
$this->parentPropertyUsedInHeredoc
code
{$this->methodUsedInHeredoc()}
code
{$this->parentMethodUsedInHeredoc()}
code
CODE;
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant
	 */
	private const UNUSED_CONSTANT_WITH_SUPPRESS = 0;

	/**
	 * @Assert\Whatever()
	 */
	private $propertyWithPrefixAnnotation;

	private $propertyUsedToCreateNewInstanceWithoutParentheses;

	public function createNewInstancesWithoutParentheses()
	{
		$this->propertyUsedToCreateNewInstanceWithoutParentheses = \stdClass::class;
		new $this->propertyUsedToCreateNewInstanceWithoutParentheses;
	}

	private $propertyUsedToCreateNewInstanceWithParentheses;

	public function createNewInstancesWithParentheses()
	{
		$this->propertyUsedToCreateNewInstanceWithParentheses = \stdClass::class;
		new $this->propertyUsedToCreateNewInstanceWithParentheses();
	}

	/**
	 * @AssertX\Whatever()
	 */
	private $propertyWithPrefixUnsetAnnotation;

	private const USED_CONSTANT_IN_STRING = false;

	public function usedConstantInString()
	{
		echo "{$this->whatever(self::USED_CONSTANT_IN_STRING)}";
		echo "{$this->whatever(self::USED_CONSTANT_FROM_PARENT_IN_STRING)}";
	}

	private $usedPrivatePropertyViaCallableInSameClass;

	private function usedPrivateMethodViaCallableInSameClass()
	{

	}

	public function callPrivateMethodOfSelfClassFromInsideCallable()
	{
		return function (self $self) {
			$self->usedPrivatePropertyViaCallableInSameClass;
			$self->usedPrivateMethodViaCallableInSameClass();
		};
	}

	private function __clone()
	{

	}

	private $propertyByReference;

	public function createReference(&$reference)
	{
		$this->propertyByReference = &$reference;
	}

	/**
	 * @Serializer\PostDeserialize()
	 */
	private function methodWithAnnotationInAlwaysUsed()
	{
	}

	/**
	 * @JMS\Serializer\Annotation\PostSerialize()
	 */
	private function methodWithAnnotationGroupInAlwaysUsed()
	{
	}
}
