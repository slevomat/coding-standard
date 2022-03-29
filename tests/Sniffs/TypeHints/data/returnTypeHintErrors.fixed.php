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
	public function arrayTypeHint(): array
	{
		return [];
	}

	/**
	 * @return int[]|null
	 */
	public function arrayTypeHintWithNull(): ?array
	{
		return [];
	}

	/**
	 * @return array{foo: int}
	 */
	public function arrayShapeTypeHint(): array
	{
		return [];
	}

	/**
	 */
	public function twoTypeWithNull(): ?string
	{
		return null;
	}

	/**
	 * @return int[]|\Traversable
	 */
	public function specificTraversable(): \Traversable
	{
		return new \Traversable();
	}

	/**
	 */
	public function &reference(): string
	{
		return 'string';
	}

	/**
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

	public function returnsVoid(): void
	{

	}

	/**
	 */
	abstract public function returnsVoidWithAnnotation(): void;

	public function closure(): \Closure
	{
		return function (): void {
		};
	}

	/**
	 * @return callable(): void
	 */
	public function returnsCallable(): callable
	{

	}

	/**
	 * @return \Traversable
	 */
	public function onlyTraversable(): \Traversable
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
	public function traversableIntersection(): \Traversable
	{
		return new \Traversable();
	}

	/**
	 * @return \Traversable&array[]
	 */
	public function traversableIntersectionDifferentOrder(): \Traversable
	{
		return new \Traversable();
	}

	/**
	 * @return null|\Traversable
	 */
	public function traversableNull(): ?\Traversable
	{
		return new \Traversable();
	}

	/**
	 */
	public function returnsObject(): object
	{
		return new \stdClass();
	}

	/**
	 * @return array<string>|array<int>
	 */
	public function unionWithSameBase(): array
	{
	}

	/**
	 * @return array<string>|array<int>|array<bool>
	 */
	public function unionWithSameBaseAndMoreTypes(): array
	{
	}

	/**
	 * @return array<int>|bool[]
	 */
	public function unionWithSameBaseToo(): array
	{
	}

	/**
	 * @return array<string>|array<int>|array<bool>|null
	 */
	public function unionWithSameNullableBase(): ?array
	{
	}

	/**
	 */
	public function nullable(): ?int
	{
	}

	/**
	 * @return mixed[]|array
	 */
	public function traversableArray(): array
	{
	}

	/***/
	public function oneLineDocComment(): string
	{
	}

	/** @return true */
	public function constTrue(): bool
	{
	}

	/** @return FALSE */
	public function constFalse(): bool
	{
	}

	/** @return 0 */
	public function constInteger(): int
	{
	}

	/** @return 0.0 */
	public function constFloat(): float
	{
	}

	/** @return 'foo' */
	public function constString(): string
	{
	}

	/** @return 'foo'|null */
	public function constNullableString(): ?string
	{
	}

	/** @return 'foo'|'bar' */
	public function constUnionString(): string
	{
	}

	/** @return class-string */
	public function classString(): string
	{
	}

	/** @return static */
	public function staticReference(): static
	{
	}

	/***/
	public function returnsMixed(): mixed
	{
	}

	/***/
	public function returnsNullableMixed(): mixed
	{
	}

	/**
	 * @return string[]
	 */
	public function uselessSuppressOfUselessAnnotation(): array
	{
	}

	/**
	 * @return string[]
	 */
	public function uselessSuppressOfMissingTraversableTypeHintSpecification(): array
	{
	}

	/**
	 * @phpstan-return class-string
	 */
	public function uselessSuppressOfMissingAnyTypeHintWithTypeHint(): string
	{
	}

	/**
	 * @phpstan-return class-string
	 */
	public function uselessSuppressOfMissingNativeTypeHintWithTypeHint(): string
	{
	}

	/**
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
	public function noReturnTypeHint(): void
	{
	}

	/**
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

}
