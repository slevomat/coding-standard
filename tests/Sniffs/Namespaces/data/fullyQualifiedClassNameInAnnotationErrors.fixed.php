<?php

namespace XXX;

use DateTime;
use DateTimeImmutable;
use Exception;
use Iterator;
use Traversable;
use YYY\Partial;
use YYY\PropertyUsed;
use TypeAlias1;
use TypeAlias2;
use SomeImportFrom1;
use SomeImportFrom2;

class Foo
{

	/** @var \XXX\PropertySameNamespace */
	private $propertySameNamespace;

	/** @var \YYY\PropertyUsed */
	private $propertyUsed;

	/** @var \YYY\Partial\PropertyPartiallyUsed */
	private $propertyPartiallyUsed;

	/** @var \YYY\PropertyFqnAlready */
	private $propertyFqnAlready;

	/**
	 * @var \XXX\Foo
	 */
	private $propertyMultiLineBlock;

	/**
	 * @var \XXX\Foo foo
	 */
	private $propertyWithComment;

	/** @var \XXX\Foo|\XXX\Bar */
	private $propertyMultipleTypes;

	/** @var \XXX\Foo[] */
	private $propertyCollection;

	/** @var string */
	private $propertyNativeType;

	/** @var self */
	private $propertySelf;

	/** @var $this */
	private $propertyThis;

	/** @var \XXX\Foo|\XXX\Foo[]|\YYY\Foo|self|mixed|null|\XXX\Foo */
	private $propertyClusterfuck;

	public function __construct()
	{
		/** @var \XXX\VariableSameNamespace $x */
		$x = true;

		/** @var $x InvalidAnnotation */
		$x = true;

		/** @var \XXX\VariableWithCommentSameNamespace $x comment*/
		$x = true;

		/** @var $variableWithoutType */
		$variableWithoutType = true;
	}

	/**
	 * @param \XXX\ParamSameNamespace $paramSameNamespace
	 * @param $paramWithoutType
	 * @return \XXX\ReturnSameNamespace
	 */
	public function baz()
	{

	}

	/**
	 * @param \YYY\Partial $partial Partial
	 */
	public function classNameInDescription($partial)
	{

	}

}

/**
 * @property \DateTimeImmutable $property
 * @property-read \Iterator $propertyRead
 * @property-write \DateTimeImmutable[] $propertyWrite
 * @method \Iterator method(\Traversable $m, \Exception ...$m2)
 * @method method2(?\DateTimeImmutable $m = null, ?\DateTimeImmutable $m2, $m3)
 * @method \DateTimeImmutable[]|array method3(?\Iterator<\DateTime> $m)
 */
class Boo
{

}

/**
 * @property \DateTimeImmutable&\Iterator $property
 */
class IntersectionAndGeneric
{

	/** @var (\DateTimeImmutable|null|\Iterator)[] */
	public $a;

	/** @var \Iterator&\Traversable<int, \DateTimeImmutable> */
	public $b;

	/** @var string&(\DateTimeImmutable|\DateTime) */
	public $c;

	/** @var string|(\DateTimeImmutable&\DateTime) */
	public $d;

	/** @var \DateTimeImmutable[][][] */
	public $e;

	/** @var (\DateTimeImmutable|\DateTime)[][][] */
	public $f;

	/** @var int|(\Iterator<string>&bool)[] */
	public $g;

	/** @var \Iterator<\Traversable<\DateTimeImmutable>> */
	public $h;

}

class CallableType
{

	/**
	 * @return callable(\Iterator $bool): \Traversable
	 */
	public function returnsCallable()
	{

	}

}

/** @var array{int, \Iterator} $arrayShape1 */
$arrayShape1 = [];

/** @var array{int: \Iterator} $arrayShape2 */
$arrayShape2 = [];

/**
 * @method method1(string $parameter = \Iterator::class)
 * @method method2(array $parameter = [\Iterator::class => \Traversable::class], $parameter2)
 */
class ConstantExpression
{

}

/**
 * @template Template of \DateTime
 * @template Template2 of \XXX\DateTimeInterface = \DateTimeImmutable
 * @template Template3 = \DateTimeImmutable
 * @template-covariant Template4 of \DateTimeImmutable
 * @template-extends \Iterator<\DateTimeImmutable>
 * @template-implements \Iterator<\DateTimeImmutable>
 * @template-use \Iterator<\DateTimeImmutable>
 */
class Template
{

	/**
	 * @phpstan-return \XXX\TemplateThatDoesNotExist
	 */
	public function withInvalidTemplate()
	{

	}

}

/**
 * @mixin \YYY\PropertyUsed
 */
class Mixin
{

}

class ConstTypeNode
{

	/**
	 * @param \DateTimeImmutable::ATOM $a
	 */
	public function constant($a)
	{
	}

	/**
	 * @param \DateTimeImmutable::* $a
	 */
	public function constantWildcard($a)
	{
	}

	/**
	 * @param \DateTimeImmutable::ATOM|\DateTime::ATOM $a
	 */
	public function constantUnion($a)
	{
	}

}

/**
 * @phpstan-type SomeTypeAlias \TypeAlias1|\TypeAlias2
 * @psalm-import-type SomeImportedType from \SomeImportFrom1
 * @phpstan-import-type AnotherImportedType from \SomeImportFrom2 as AnotherImportedType2
 */
class TypeAliasAndImportes
{

	/**
	 * @param SomeImportedType $a
	 * @param AnotherImportedType2 $b
	 * @return SomeTypeAlias
	 */
	public function types($a, $b)
	{

	}

}

class Conditional
{

	/**
	 * @return (\YYY\Partial\Conditional1 is \YYY\Partial\Conditional2 ? (\YYY\Partial\Conditional3|\YYY\Partial\Conditional4) : (\YYY\Partial\Conditional4 is not \YYY\Partial\Conditional5 ? \YYY\Partial\Conditional6 : \YYY\Partial\Conditional7))
	 */
	public function withConditional()
	{
	}

	/**
	 * @return ($parameter is \YYY\Partial\Conditional8 ? (\YYY\Partial\Conditional9|\YYY\Partial\Conditional10) : \YYY\Partial\Conditional10)
	 */
	public function withConditionalParameter($parameter)
	{
	}

}
