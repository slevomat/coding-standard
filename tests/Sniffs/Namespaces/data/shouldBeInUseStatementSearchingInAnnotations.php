<?php

namespace Foo\Test\Bla;

use Foo\Anything;
use Foo\Something;

/**
 * @method \DateTimeImmutable|int|\Foo\DateTime getProperty()
 * @method doSomething(\Foo\DateTime $date, $mixed)
 */
abstract class Bar
{

	/** @var \DateTimeImmutable|int|\Foo\DateTime */
	private $property;

	/**
	 * @var \Foo\DateTime
	 */
	private $property2;

	/**
	 * @ORM\Column \Foo\Anything
	 */
	public function noErrors()
	{
		/**@var*/
	}

	/**
	 * Description
	 *
	 * @param array|\Foo\ArrayObject|iterable $iterable Parameter description
	 * @param \Boo\Anything $anything
	 * @return \Foo\Something[] Return description
	 * @throws \Foo\Exception|SomeException Throws description
	 */
	public function method()
	{
		/** @var \Foo\Traversable|array $variable */

		/** @var BlaBla\Foo @variable2 */
	}

	/**
	 * @return \Foo\Something
	 */
	public function method2()
	{
	}

	/**
	 * @param \Foo\Something $something
	 * @return void
	 */
	abstract function abstractMethod(Something $something);

	/**
	 * @param $parameter
	 */
	public function invalidAnnotation($parameter)
	{

	}

}

/**
 * @property \DateTimeImmutable&\Foo\DateTime $property
 */
class IntersectionAndGeneric
{

	/** @var (\DateTimeImmutable|null|\Foo\DateTime)[] */
	public $a;

	/** @var \Foo\ArrayObject&\Traversable<int, \DateTimeImmutable> */
	public $b;

	/** @var string&(\DateTimeImmutable|\Foo\DateTime) */
	public $c;

	/** @var string|(\DateTimeImmutable&\Foo\DateTime) */
	public $d;

	/** @var \Foo\DateTime[][][] */
	public $e;

	/** @var (\DateTimeImmutable|\Foo\DateTime)[][][] */
	public $f;

	/** @var int|(\Foo\ArrayObject<string>&BlaBla\Foo)[] */
	public $g;

	/** @var \Foo\ArrayObject<\Foo\ArrayObject<\Foo\DateTime>> */
	public $h;

}

class CallableType
{

	/**
	 * @return callable(\Foo\DateTime $bool): \Foo\ArrayObject
	 */
	public function returnsCallable()
	{

	}

}

/** @var array{int, \Foo\DateTime} $arrayShape1 */
$arrayShape1 = [];

/** @var array{int: \Foo\DateTime, ...} $arrayShape2 */
$arrayShape2 = [];

/**
 * @method method1(string $parameter = \Foo\DateTime::class)
 * @method method2(array $parameter = [\Foo\DateTime::class => \Foo\ArrayObject::class], $parameter2)
 */
class ConstantExpression
{

}

class Whatever
{

	/** @var \Foo\Test\Bla\Whatever */
	private $whatever;

	/** @var Anything */
	private $anything;

	/** @var \Foo\Different\Anything */
	private $anythingDifferent;

}

class CallableInArray
{
	/** @var (callable(): \Foo\Anything)[] */
	private $callableInArray;
}

class Conditional
{

	/**
	 * @return (\Foo\Conditional1 is \Foo\Conditional2 ? (\Foo\Conditional3|\Foo\Conditional4) : (\Foo\Conditional4 is not \Foo\Conditional5 ? \Foo\Conditional6 : \Foo\Conditional7))
	 */
	public function withConditional()
	{
	}

	/**
	 * @return ($parameter is \Foo\Conditional8 ? (\Foo\Conditional9|\Foo\Conditional10) : \Foo\Conditional10)
	 */
	public function withConditionalParameter($parameter)
	{
	}

}

class OffsetAccess
{

	/**
	 * @return \Foo\OffsetAccessType[\Foo\OffsetAccessOffset]|\Foo\OffsetAccessType2[\Foo\OffsetAccessOffset2]|\Foo\OffsetAccessType3[array{offset: \Foo\OffsetAccessOffset3}]
	 */
	public function returnOffsetAccess()
	{}

}

class Assert
{

	/**
	 * @phpstan-assert \Foo\Assertion $parameter
	 */
	public function phpstanAssert($parameter)
	{
	}

	/**
	 * @phpstan-assert-if-true \Foo\Assertion $parameter
	 */
	public function phpstanAssertIfTrue($parameter)
	{
	}

	/**
	 * @phpstan-assert-if-false \Foo\Assertion $parameter
	 */
	public function phpstanAssertIfFalse($parameter)
	{
	}

	/**
	 * @psalm-assert \Foo\Assertion $parameter
	 */
	public function psalmAssert($parameter)
	{
	}

	/**
	 * @psalm-assert-if-true \Foo\Assertion $parameter
	 */
	public function psalmAssertIfTrue($parameter)
	{
	}

	/**
	 * @psalm-assert-if-false \Foo\Assertion $parameter
	 */
	public function psalmAssertIfFalse($parameter)
	{
	}

}

class GenericTypeProjections
{
	/**
	 * @param \Foo\Anything<covariant \Foo\Something, \Foo\Something> $parameter
	 */
	public function covariant($parameter)
	{
	}
}

/**
 * @method static bool compare<T1, T2 of \Foo\Anything, T3 = \Foo\Something>(T1 $t1, T2 $t2, T3 $t3)
 */
class MethodWithGenerics
{
}
