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

/** @var array{int: \Foo\DateTime} $arrayShape2 */
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
