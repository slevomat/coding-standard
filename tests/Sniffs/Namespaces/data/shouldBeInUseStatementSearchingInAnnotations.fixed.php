<?php

namespace Foo\Test\Bla;

use Foo\Anything;
use Foo\Something;
use Foo\DateTime;
use Foo\ArrayObject;
use Foo\Exception;
use Foo\Traversable;

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

/** @var array{int: DateTime} $arrayShape2 */
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
