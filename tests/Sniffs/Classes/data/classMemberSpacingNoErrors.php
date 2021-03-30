<?php // lint >= 8.0

class EmptyClass
{

}

class ClassWithOneUse
{

	use SomeTrait;

}

class ClassWithOneConstant
{

	public const WHATEVER = 0;

}

class ClassWithOneProperty
{

	public $whatever = 0;

}

class ClassWithOneMethod
{

	public function __construct()
	{
	}

}

abstract class WithoutErrors
{

	use SomeTrait;
	use AnotherTrait {
		doSomething as public;
		doSomethingElse as public;
	}

	const ONE = 1;


	private const TWO = 2;

	/**
	 * @var int
	 */
	public $one = 1;
	/** @var string|null */
	private $two;

	#[AttributeBeforeComment]
	/**
	 * @return object
	 */
	public function one()
	{
		return new class
		{

			public $property;

		};

	}






	final public function two()
	{

	} // Fucking comment

	public $third;

	/**
	 * @return mixed
	 */
	#[SomeAttribute]
	abstract public function thirdWithAttributeAndDocDomment();

	/**
	 * @codingStandardsIgnoreStart
	 *
	 * @var string
	 */
	protected $forth;
	// @codingStandardsIgnoreEnd

	#[SomeAttribute]
	#[JustAnotherAttribute]
	public function forthWithAttribute()
	{
	}

	// Invalid comment
	public const THIRD = [3];

	/*
	 Invalid comment
	*/
	public function fifth()
	{
	}

	public function __construct(private $propertyPromotion)
	{
	}

}
