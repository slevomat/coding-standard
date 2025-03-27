<?php // lint >= 8.0

abstract class Whatever
{

	public function __construct()
	{
	}

	/** @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint */
	private function isSniffSuppressed()
	{
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	private function hasInheritdocAnnotation()
	{
		return true;
	}

	/**
	 * @deprecated
	 *
	 * {@inheritdoc}
	 */
	private function hasInheritdocAnnotationWithOtherAnnotation()
	{
		return true;
	}

	/**
	 * Description.
	 *
	 * {@inheritDoc}
	 */
	public function hasDescriptionAndInheritDocAnnotation()
	{
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingAnyTypeHint
	 */
	private function isSniffCodeAnyTypeHintSuppressed()
	{
		return true;
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
	 * @return int
	 */
	private function isSniffCodeMissingNativeTypeHintSuppressed()
	{
		return 0;
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
	 * @return void
	 */
	abstract public function isSniffCodeMissingNativeTypeHintSuppressedWithVoid();

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.UselessAnnotation
	 * @return array
	 */
	private function isSniffCodeMissingTravesableTypeHintSpecificationSuppressed(): array
	{
		return [];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.UselessAnnotation
	 * @return int
	 */
	private function isSniffCodeUselessAnnotationSuppressed(): int
	{
		return 0;
	}

	private function noTraversableType(): int
	{
		return 0;
	}

	/**
	 * @return int[]
	 */
	private function withTraversableTypeHintSpecification(): array
	{
		return [];
	}

	/**
	 * @return null
	 */
	public function nullReturnValue()
	{
		return null;
	}

	/**
	 * @return string|int|bool
	 */
	public function aLotOfTypes()
	{
		return 0;
	}

	/**
	 * @return string|int
	 */
	public function twoTypeNoNullOrTraversable()
	{
		return 0;
	}

	/**
	 * @return scalar
	 */
	public function invalidType()
	{
		return 0;
	}

	/**
	 * @return int[]|\DateTimeImmutable
	 */
	public function twoTypesNoTraversable()
	{
		return [];
	}

	/**
	 * @return \Boo<bool>|\Foo
	 */
	public function generic()
	{
		return new \Boo();
	}

	/**
	 * @return
	 */
	public function emptyAnnotation(): int
	{
		return 0;
	}

	/**
	 * @return $this
	 */
	public function containsThis(): self
	{
		return $this;
	}

	/**
	 * @return $this|null
	 */
	public function containsThisOrNull(): ?self
	{
		return $this;
	}

	public function closureThatReturnsSomething(): \Closure
	{
		return function () {
			return true;
		};
	}

	public function closureWithTypeHint(): \Closure
	{
		return function (): void {
		};
	}

	/**
	 * @return $this
	 */
	public function returnsThis()
	{
		return $this;
	}

	/**
	 * @return array<string, callable(mixed $value) : string>
	 */
	public function callableArray(): array
	{

	}

	/**
	 * @return Whatever|Something|Anything
	 */
	public function unionWithDifferentBase()
	{
	}

	/**
	 * @return array<int>|array<bool>|(A&B)
	 */
	public function unionWithMoreDifferentBase()
	{
	}

	/**
	 * @return Whatever|Something|Anything|null
	 */
	public function unionWithDifferentNullableBase($a)
	{
	}

	/**
	 * @return mixed[]|array|Traversable
	 */
	public function moreTraverasableTypes()
	{
	}

	/**
	 * @return mixed[]|array|SomethingThatLooksAsArray
	 */
	public function moreDifferentTypes()
	{
	}

	/**
	 * @return int[]|string[]|Anything
	 */
	public function anotherDifferentTypes()
	{
	}

	/**
	 * @return Whatever|mixed[]|array
	 */
	public function yetAnotherDifferentTypes()
	{
	}

	/**
	 * @psalm-return Whatever<int>
	 */
	public function withPsalmAnnotationAndMissingNativeTypeHint()
	{
		return [];
	}

	/**
	 * @phpstan-return Whatever<int>
	 */
	public function withPhpstanAnnotationAndMissingNativeTypeHint()
	{
		return [];
	}

	/**
	 * @psalm-return
	 * @psalm-return Whatever<int>
	 */
	public function withPsalmAnnotationAndTraversableNativeTypeHint(): array
	{
	}

	/**
	 * @phpstan-return
	 * @phpstan-return Whatever<int> $a
	 */
	public function withPhpstanAnnotationAndTraversableNativeTypeHint(): array
	{
	}

	/** @return array<int, ?Whatever> */
	public function traversableWithNullableItem(): array
	{

	}

	/** @return class-string */
	public function classString(): string
	{
	}

	/**
	 * @return \Foo::INTEGER
	 */
	public function constTypeNode()
	{

	}

}

/**
 * @return array{a: string, b: ?int}
 */
function arrayShape(): array
{
}

/**
 * @return array{params: array{video_id: int}, type: "http", url: string}
 */
function arrayShape2(): array
{
}

/**
 * @phpstan-type SomeAlias1 int|false
 * @phpstan-import-type SomeAlias2 from \SomeClass
 * @phpstan-import-type SomeAlias4 from \SomeClass as SomeAlias3
 */
class Aliases
{

	/**
	 * @return SomeAlias1
	 */
	public function withAlias1()
	{
	}

	/**
	 * @return SomeAlias2
	 */
	public function withAlias2()
	{
	}

	/**
	 * @return SomeAlias3
	 */
	public function withAlias3()
	{
	}

	/**
	 * @return SomeAlias3
	 */
	public function withArrayAlias(): array
	{
	}

	/**
	 * @return SomeAlias2|null
	 */
	public function withNullableArrayAlias(): ?array
	{
	}

	/**
	 * @return (Conditional is Conditional2 ? int : false)
	 */
	public function withConditional(): int|false
	{
	}

	/**
	 * @return ($parameter is Conditional2 ? int : false)
	 */
	public function withConditionalParameter($parameter): int|false
	{
	}

	/**
	 * @return (Conditional is Conditional2 ? int[] : bool)
	 */
	public function withConditionalArrayInIf(): array
	{
	}

	/**
	 * @return (Conditional is Conditional2 ? bool : int[])
	 */
	public function withConditionalArrayInElse(): array
	{
	}

	/**
	 * @return ($parameter is Conditional2 ? int[] : bool)
	 */
	public function withParameterConditionalArrayInIf($parameter): array
	{
	}

	/**
	 * @return ($parameter is Conditional2 ? bool : int[])
	 */
	public function withParameterConditionalArrayInElse($parameter): array
	{
	}

}
