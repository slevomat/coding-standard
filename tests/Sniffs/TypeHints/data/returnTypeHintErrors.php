<?php // lint >= 7.2

abstract class Whatever
{

	private function noTypeHintNoAnnotation()
	{
		return true;
	}

	/**
	 * @return int[]
	 */
	public function arrayTypeHint()
	{
		return [];
	}

	/**
	 * @return int[]|null
	 */
	public function arrayTypeHintWithNull()
	{
		return [];
	}

	/**
	 * @return array{foo: int}
	 */
	public function arrayShapeTypeHint()
	{
		return [];
	}

	/**
	 * @return string|null
	 */
	public function twoTypeWithNull()
	{
		return null;
	}

	/**
	 * @return int[]|\Traversable
	 */
	public function specificTraversable()
	{
		return new \Traversable();
	}

	/**
	 * @return string
	 */
	public function &reference()
	{
		return 'string';
	}

	/**
	 * @return string
	 */
	public function uselessAnnotation(): string
	{
		return 'string';
	}

	public function missingAnnotationForTraversable(): array
	{
		return [];
	}

	/**
	 * @return array
	 */
	public function missingItemsSpecification(): array
	{
		return [];
	}

	public function returnsVoid()
	{

	}

	/**
	 * @return void
	 */
	abstract public function returnsVoidWithAnnotation();

	public function closure(): \Closure
	{
		return function () {
		};
	}

	/**
	 * @return callable(): void
	 */
	public function returnsCallable()
	{

	}

	/**
	 * @return \Traversable
	 */
	public function onlyTraversable()
	{

	}

	/**
	 * @return array{array}
	 */
	public function arrayShapeWithoutItemsSpecification(): array
	{

	}

	/**
	 * @return \Generic<array>
	 */
	public function genericWithoutItemsSpecification(): \Generic
	{

	}

	/**
	 * @return array[]&\Traversable
	 */
	public function traversableIntersection()
	{
		return new \Traversable();
	}

	/**
	 * @return \Traversable&array[]
	 */
	public function traversableIntersectionDifferentOrder()
	{
		return new \Traversable();
	}

	/**
	 * @return null|\Traversable
	 */
	public function traversableNull()
	{
		return new \Traversable();
	}

	/**
	 * @return object
	 */
	public function returnsObject()
	{
		return new \stdClass();
	}

	/**
	 * @return array<string>|array<int>
	 */
	public function unionWithSameBase()
	{
	}

	/**
	 * @return array<string>|array<int>|array<bool>
	 */
	public function unionWithSameBaseAndMoreTypes()
	{
	}

	/**
	 * @return array<int>|bool[]
	 */
	public function unionWithSameBaseToo()
	{
	}

	/**
	 * @return array<string>|array<int>|array<bool>|null
	 */
	public function unionWithSameNullableBase()
	{
	}

	/**
	 * @return ?int
	 */
	public function nullable()
	{
	}

	/**
	 * @return mixed[]|array
	 */
	public function traversableArray()
	{
	}

	/** @return string */
	public function oneLineDocComment(): string
	{
	}

	/** @return true */
	public function constTrue()
	{
	}

	/** @return FALSE */
	public function constFalse()
	{
	}

	/** @return 0 */
	public function constInteger()
	{
	}

	/** @return 0.0 */
	public function constFloat()
	{
	}

	/** @return 'foo' */
	public function constString()
	{
	}

	/** @return 'foo'|null */
	public function constNullableString()
	{
	}

	/** @return 'foo'|'bar' */
	public function constUnionString()
	{
	}

	/** @return class-string */
	public function classString()
	{
	}

}
