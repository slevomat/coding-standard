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
	#[SomeAttribute]
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

/**
 * @phpstan-type SomeTypeAlias \TypeAlias1|\TypeAlias2
 * @psalm-import-type SomeImportedType from \SomeImportFrom1
 * @phpstan-import-type AnotherImportedType from \SomeImportFrom2 as AnotherImportedType2
 */
class TypeAliasAndImports
{

	/**
	 * @param SomeImportedType $a
	 * @param AnotherImportedType2 $b
	 * @return SomeTypeAlias
	 */
	public function types($a, $b)
	{
		/** @psalm-var SomeTypeAlias $someTypeAlias */
		$someTypeAlias = 'someTypeAlias';

		return $someTypeAlias;
	}

}

class NeverIsType
{

	/**
	 * @return never
	 */
	public function withNever()
	{
	}

}

/**
 * @method sortBy(callable $path, int $order = \SORT_DESC, int $sort = \SORT_NUMERIC, int $flags = self::FLAG)
 */
class ConstantExpression
{
}

/**
 * @param int<min, 100> $range
 * @return int<0, max>
 */
function intRange(array $range): array
{
}
