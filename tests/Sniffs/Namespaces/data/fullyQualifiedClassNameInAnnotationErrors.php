<?php

namespace XXX;

use DateTime;
use DateTimeImmutable;
use Exception;
use Iterator;
use Traversable;
use YYY\Partial;
use YYY\PropertyUsed;

class Foo
{

	/** @var PropertySameNamespace */
	private $propertySameNamespace;

	/** @var PropertyUsed */
	private $propertyUsed;

	/** @var Partial\PropertyPartiallyUsed */
	private $propertyPartiallyUsed;

	/** @var \YYY\PropertyFqnAlready */
	private $propertyFqnAlready;

	/**
	 * @var Foo
	 */
	private $propertyMultiLineBlock;

	/**
	 * @var Foo foo
	 */
	private $propertyWithComment;

	/** @var Foo|Bar */
	private $propertyMultipleTypes;

	/** @var Foo[] */
	private $propertyCollection;

	/** @var string */
	private $propertyNativeType;

	/** @var self */
	private $propertySelf;

	/** @var $this */
	private $propertyThis;

	/** @var Foo|Foo[]|\YYY\Foo|self|mixed|null|Foo */
	private $propertyClusterfuck;

	public function __construct()
	{
		/** @var VariableSameNamespace $x */
		$x = true;

		/** @var $x InvalidAnnotation */
		$x = true;

		/** @var VariableWithCommentSameNamespace $x comment*/
		$x = true;

		/** @var $variableWithoutType */
		$variableWithoutType = true;
	}

	/**
	 * @param ParamSameNamespace $paramSameNamespace
	 * @param $paramWithoutType
	 * @return ReturnSameNamespace
	 */
	public function baz()
	{

	}

	/**
	 * @param Partial $partial Partial
	 */
	public function classNameInDescription($partial)
	{

	}

}

/**
 * @property DateTimeImmutable $property
 * @property-read Iterator $propertyRead
 * @property-write DateTimeImmutable[] $propertyWrite
 * @method Iterator method(Traversable $m, Exception ...$m2)
 * @method method2(?DateTimeImmutable $m = null, ?DateTimeImmutable $m2, $m3)
 * @method DateTimeImmutable[]|array method3(?Iterator<DateTime> $m)
 */
class Boo
{

}

/**
 * @property DateTimeImmutable&Iterator $property
 */
class IntersectionAndGeneric
{

	/** @var (DateTimeImmutable|null|Iterator)[] */
	public $a;

	/** @var Iterator&Traversable<int, DateTimeImmutable> */
	public $b;

	/** @var string&(DateTimeImmutable|DateTime) */
	public $c;

	/** @var string|(DateTimeImmutable&DateTime) */
	public $d;

	/** @var DateTimeImmutable[][][] */
	public $e;

	/** @var (DateTimeImmutable|DateTime)[][][] */
	public $f;

	/** @var int|(Iterator<string>&bool)[] */
	public $g;

	/** @var Iterator<Traversable<DateTimeImmutable>> */
	public $h;

}

class CallableType
{

	/**
	 * @return callable(Iterator $bool): Traversable
	 */
	public function returnsCallable()
	{

	}

}

/** @var array{int, Iterator} $arrayShape1 */
$arrayShape1 = [];

/** @var array{int: Iterator} $arrayShape2 */
$arrayShape2 = [];

/**
 * @method method1(string $parameter = Iterator::class)
 * @method method2(array $parameter = [Iterator::class => Traversable::class], $parameter2)
 */
class ConstantExpression
{

}

/**
 * @template Template of DateTime
 * @template-covariant Template2 of DateTimeImmutable
 * @template-extends Iterator<DateTimeImmutable>
 * @template-implements Iterator<DateTimeImmutable>
 * @template-use Iterator<DateTimeImmutable>
 */
class Template
{

	/**
	 * @phpstan-return TemplateThatDoesNotExist
	 */
	public function withInvalidTemplate()
	{

	}

}

/**
 * @mixin PropertyUsed
 */
class Mixin
{

}

class ConstTypeNode
{

	/**
	 * @param DateTimeImmutable::ATOM $a
	 */
	public function constant($a)
	{
	}

	/**
	 * @param DateTimeImmutable::* $a
	 */
	public function constantWildcard($a)
	{
	}

	/**
	 * @param DateTimeImmutable::ATOM|DateTime::ATOM $a
	 */
	public function constantUnion($a)
	{
	}

}
