<?php // lint >= 8.0

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

	/** @return static */
	public function staticReference()
	{
	}

	/** @return mixed */
	public function returnsMixed()
	{
	}

	/** @return mixed|null */
	public function returnsNullableMixed()
	{
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.UselessAnnotation
	 * @return string[]
	 */
	public function uselessSuppressOfUselessAnnotation(): array
	{
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
	 * @return string[]
	 */
	public function uselessSuppressOfMissingTraversableTypeHintSpecification(): array
	{
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingAnyTypeHint
	 * @phpstan-return class-string
	 */
	public function uselessSuppressOfMissingAnyTypeHintWithTypeHint(): string
	{
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
	 * @phpstan-return class-string
	 */
	public function uselessSuppressOfMissingNativeTypeHintWithTypeHint(): string
	{
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
	 * @return scalar
	 */
	public function uselessSuppressOfMissingNativeTypeHint()
	{
	}

	/**
	 * @return void
	 */
	public function voidButReturnsValue()
	{
		return true;
	}

	/**
	 * @return no-return
	 */
	public function noReturnTypeHint()
	{
	}

	/**
	 * @return ?int
	 */
	public function uselessAnnotationWithShortNullable(): ?int
	{
		return 0;
	}

	/**
	 * @return (Conditional is Conditional2 ? array : iterable)
	 */
	public function withConditional(): array
	{
	}

	/** @return positive-int */
	public function returnsPositiveInteger()
	{
	}

	/** @return negative-int */
	public function returnsNegativeInteger()
	{
	}

	/** @return non-empty-array<int> */
	public function returnsNonEmptyArray()
	{
	}

	/** @return list<int> */
	public function returnsList()
	{
	}

	/** @return non-empty-list<int> */
	public function returnsNonEmptyList()
	{
	}

	/** @return non-empty-string */
	public function returnsNonEmptyString()
	{
	}

	/** @return non-falsy-string */
	public function returnsNonFalseString()
	{
	}

	/** @return literal-string */
	public function returnsLiteralString()
	{
	}

	/** @return object{a: int} */
	public function returnsObjectShape()
	{
	}

}
