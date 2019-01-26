<?php

namespace XXX;

use DateTimeImmutable;
use Exception;
use Iterator;
use Traversable;
use YYY\Partial;
use YYY\PropertyUsed;

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
 * @method method(?\DateTimeImmutable $m = null, ?\DateTimeImmutable $m2, $m3)
 * @method \DateTimeImmutable[]|array method(\Iterator $m)
 */
class Boo
{

}
