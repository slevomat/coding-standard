<?php

namespace XXX;

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
