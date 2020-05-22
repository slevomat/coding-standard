<?php

namespace FooNamespace;

use stdClass;

/**
 * @template TemplateAboveClass
 */
class FooClass
{

	/** @var boolean */
	const BOOLEAN = true;

	/**
	 * @var mixed[]
	 */
	private $array = [];

	/**
	 * @param string $foo
	 */
	public function __construct(string $foo)
	{
		/** @var self $boo */
		$boo = $this->get();

		/** @var $coo self */
		$coo = $this->get();

		/** @var $missingTypeDefinition */
		$missingTypeDefinition = [];
	}

	/**
	 * @return self|static|$this
	 * @return \Exception
	 */
	public function create()
	{
		return new self('foo');
	}

	/**
	 * @return bool|true|false
	 */
	private function get()
	{
		return true;
	}

	/**
	 * @return void
	 */
	public function returnVoid()
	{

	}

	/**
	 * @return \FooNamespace\FooClass
	 */
	public function returnClass()
	{

	}

	/**
	 * @param string|int|\DateTimeImmutable $date
	 * @return \DateTimeImmutable|null
	 */
	public function createDate($date)
	{

	}

	/**
	 * @return array|mixed[]|\Traversable
	 */
	public function traversable()
	{

	}

	/**
	 * @return \FooNamespace\FooClass[]|\Iterator
	 */
	public function resultSet()
	{

	}

	/**
	 * @see
	 */
	public function unsupportedAnnotation()
	{

	}

	/**
	 * @return
	 */
	public function invalidAnnotation()
	{

	}

	/**
	 * @template TemplateAboveMethod
	 * @return TemplateAboveMethod
	 */
	public function usingTemplateAboveMethod()
	{

	}

	/**
	 * @return TemplateAboveClass
	 */
	public function usingTemplateAboveClass()
	{
		/** @var TemplateAboveClass $instance */
		$instance = new \stdClass();

		return $instance;
	}

	/** @var TemplateAboveClass */
	public $usingTemplateAboveClass;

}

/**
 * @template TemplateForMixin
 * @mixin TemplateForMixin
 */
class Mixin
{

}
