<?php // lint >= 7.4

namespace Foo\Test\Bla;

use Foo\Anything;
use Foo\Something;
use Foo\DateTime;
use Foo\ArrayObject;
use Foo\Exception;
use Foo\Traversable;
use Foo\Conditional1;
use Foo\Conditional2;
use Foo\Conditional3;
use Foo\Conditional4;
use Foo\Conditional5;
use Foo\Conditional6;
use Foo\Conditional7;
use Foo\Conditional8;
use Foo\Conditional9;
use Foo\Conditional10;
use Foo\OffsetAccessType;
use Foo\OffsetAccessOffset;
use Foo\OffsetAccessType2;
use Foo\OffsetAccessOffset2;
use Foo\OffsetAccessType3;
use Foo\OffsetAccessOffset3;
use Foo\Assertion;
use Foo\ObjectShapeItem1;
use Foo\ObjectShapeItem2;

/**
 * @method \DateTimeImmutable|int|DateTime getProperty()
 * @method doSomething(DateTime $date, $mixed)
 */
abstract class Bar
{

	/** @var \DateTimeImmutable|int|DateTime */
	private $property;

	/**
	 * @var DateTime
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
	 * @param array|ArrayObject|iterable $iterable Parameter description
	 * @param \Boo\Anything $anything
	 * @return Something[] Return description
	 * @throws Exception|SomeException Throws description
	 */
	public function method()
	{
		/** @var Traversable|array $variable */

		/** @var BlaBla\Foo @variable2 */
	}

	/**
	 * @return Something
	 */
	public function method2()
	{
	}

	/**
	 * @param Something $something
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
 * @property \DateTimeImmutable&DateTime $property
 */
class IntersectionAndGeneric
{

	/** @var (\DateTimeImmutable|null|DateTime)[] */
	public $a;

	/** @var ArrayObject&\Traversable<int, \DateTimeImmutable> */
	public $b;

	/** @var string&(\DateTimeImmutable|DateTime) */
	public $c;

	/** @var string|(\DateTimeImmutable&DateTime) */
	public $d;

	/** @var DateTime[][][] */
	public $e;

	/** @var (\DateTimeImmutable|DateTime)[][][] */
	public $f;

	/** @var int|(ArrayObject<string>&BlaBla\Foo)[] */
	public $g;

	/** @var ArrayObject<ArrayObject<DateTime>> */
	public $h;

}

class CallableType
{

	/**
	 * @return callable(DateTime $bool): ArrayObject
	 */
	public function returnsCallable()
	{

	}

}

/** @var array{int, DateTime} $arrayShape1 */
$arrayShape1 = [];

/** @var array{int: DateTime, ...} $arrayShape2 */
$arrayShape2 = [];

/**
 * @method method1(string $parameter = DateTime::class)
 * @method method2(array $parameter = [DateTime::class => ArrayObject::class], $parameter2)
 */
class ConstantExpression
{

}

class Whatever
{

	/** @var Whatever */
	private $whatever;

	/** @var Anything */
	private $anything;

	/** @var \Foo\Different\Anything */
	private $anythingDifferent;

}

class CallableInArray
{
	/** @var (callable(): Anything)[] */
	private $callableInArray;
}

class Conditional
{

	/**
	 * @return (Conditional1 is Conditional2 ? Conditional3|Conditional4 : (Conditional4 is not Conditional5 ? Conditional6 : Conditional7))
	 */
	public function withConditional()
	{
	}

	/**
	 * @return ($parameter is Conditional8 ? Conditional9|Conditional10 : Conditional10)
	 */
	public function withConditionalParameter($parameter)
	{
	}

}

class OffsetAccess
{

	/**
	 * @return OffsetAccessType[OffsetAccessOffset]|OffsetAccessType2[OffsetAccessOffset2]|OffsetAccessType3[array{offset: OffsetAccessOffset3}]
	 */
	public function returnOffsetAccess()
	{}

}

class Assert
{

	/**
	 * @phpstan-assert Assertion $parameter
	 */
	public function phpstanAssert($parameter)
	{
	}

	/**
	 * @phpstan-assert-if-true Assertion $parameter
	 */
	public function phpstanAssertIfTrue($parameter)
	{
	}

	/**
	 * @phpstan-assert-if-false Assertion $parameter
	 */
	public function phpstanAssertIfFalse($parameter)
	{
	}

	/**
	 * @psalm-assert Assertion $parameter
	 */
	public function psalmAssert($parameter)
	{
	}

	/**
	 * @psalm-assert-if-true Assertion $parameter
	 */
	public function psalmAssertIfTrue($parameter)
	{
	}

	/**
	 * @psalm-assert-if-false Assertion $parameter
	 */
	public function psalmAssertIfFalse($parameter)
	{
	}

}

class GenericTypeProjections
{
	/**
	 * @param Anything<covariant Something, Something> $parameter
	 */
	public function covariant($parameter)
	{
	}
}

/**
 * @method static bool compare<T1, T2 of Anything, T3 = Something>(T1 $t1, T2 $t2, T3 $t3)
 */
class MethodWithGenerics
{
}

class ObjectShape
{

	/** @var object{a: ObjectShapeItem1, b: object{c: ObjectShapeItem2}} */
	public object $object;

}
