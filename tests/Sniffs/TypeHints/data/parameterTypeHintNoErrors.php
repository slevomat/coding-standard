<?php // lint >= 8.0

use Doctrine\Common\Collections\ArrayCollection;

class Whatever
{

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
	 */
	private function isSniffSuppressed($a)
	{

	}

	/**
	 * {@inheritdoc}
	 */
	private function hasInheritdocAnnotation($a)
	{

	}

	/**
	 * @inheritdoc
	 */
	private function hasInheritdocAnnotation2($a)
	{

	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingAnyTypeHint
	 */
	private function isSniffCodeAnyTypeHintSuppressed($a)
	{

	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $a
	 */
	private function isSniffCodeMissingNativeTypeHintSuppressed($a)
	{

	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingTraversableTypeHintSpecification
	 * @param array $a
	 */
	private function isSniffCodeMissingTravesableTypeHintSpecificationSuppressed(array $a)
	{

	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.UselessAnnotation
	 * @param int $a
	 */
	private function isSniffCodeUselessAnnotationSuppressed(int $a)
	{

	}

	private function noTraversableType(int $a)
	{

	}

	/**
	 * @param int[] $a
	 */
	private function withTraversableTypeHintSpecification(array $a)
	{

	}

	/**
	 * @param null $a
	 */
	public function nullParameter($a): void
	{
	}

	/**
	 * @param string|int|bool $a
	 */
	public function aLotOfTypesParameter($a): void
	{
	}

	/**
	 * @param string|int $a
	 */
	public function twoTypeNoNullOrTraversable($a): void
	{
	}

	/**
	 * @param scalar $a
	 */
	public function invalidType($a): void
	{
	}

	/**
	 * @param int[]|\DateTimeImmutable $a
	 */
	public function twoTypesNoTraversable($a)
	{

	}

	/**
	 * @param \Boo<bool>|\Foo $a
	 */
	public function generic($a)
	{
	}

	/**
	 * @param
	 */
	public function emptyAnnotation(int $a)
	{

	}

	/**
	 * @param invalid invalid $a
	 */
	public function invalidAnnotation(int $a)
	{

	}

	/**
	 * @param $a
	 */
	public function noType(int $a)
	{

	}

	/**
	 * @param $this $a
	 */
	public function containsThis(self $a)
	{

	}

	/**
	 * @param $this|null $a
	 */
	public function containsThisOrNull(?self $a)
	{

	}

	/**
	 * @param array<string, callable(mixed $value) : string> $a
	 */
	public function callableArray(array $a): void
	{

	}

	/**
	 * @param Whatever|Something|Anything $a
	 */
	public function unionWithDifferentBase($a)
	{
	}

	/**
	 * @param array<int>|array<bool>|(A&B) $a
	 */
	public function unionWithMoreDifferentBase($a)
	{
	}

	/**
	 * @param Whatever|Something|Anything|null $a
	 */
	public function unionWithDifferentNullableBase($a)
	{
	}

	/**
	 * @param mixed[]|array|Traversable $a
	 */
	public function moreTraverasableTypes($a)
	{
	}

	/**
	 * @param mixed[]|array|SomethingThatLooksAsArray $a
	 */
	public function moreDifferentTypes($a)
	{
	}

	/**
	 * @param int[]|string[]|Anything $a
	 */
	public function anotherDifferentTypes($a)
	{
	}

	/**
	 * @param Whatever|mixed[]|array $a
	 */
	public function yetAnotherDifferentTypes($a)
	{
	}

	/**
	 * @psalm-param Whatever<int> $a
	 */
	public function withPsalmAnnotationAndMissingNativeTypeHint($a)
	{
	}

	/**
	 * @phpstan-param Whatever<int> $a
	 */
	public function withPhpstanAnnotationAndMissingNativeTypeHint($a)
	{
	}

	/**
	 * @psalm-param
	 * @psalm-param invalid invalid $b Invalid
	 * @psalm-param Whatever<int> $a
	 */
	public function withPsalmAnnotationAndTraversableNativeTypeHint(array $a)
	{
	}

	/**
	 * @phpstan-param
	 * @phpstan-param invalid invalid $b Invalid
	 * @phpstan-param Whatever<int> $a
	 */
	public function withPhpstanAnnotationAndTraversableNativeTypeHint(array $a)
	{
	}

	/** @param array<int, ?Whatever> $a */
	public function traversableWithNullableItem(array $a)
	{
	}

	/**
	 * @param \Foo::INTEGER $a
	 */
	public function constTypeNode($a)
	{

	}

	/** @param class-string $a */
	public function classString(string $a)
	{
	}

	/**
	 * @param int   $a )
	 * @param int[] $b
	 */
	public function brokenParameterDescription(int $a, array $b)
	{
	}

}

/**
 * @param array{a: string, b: ?int} $a
 */
function arrayShape(array $a)
{
}

/**
 * @param array{params: array{video_id: int}, type: "http", url: string} $a
 */
function arrayShape2(array $a)
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
	 * @param SomeAlias1 $withAlias1
	 * @param SomeAlias2 $withAlias2
	 * @param SomeAlias3 $withAlias3
	 */
	public function withAlias($withAlias1, $withAlias2, $withAlias3)
	{
	}

	/**
	 * @param SomeAlias1 $array
	 */
	public function withArrayAlias(array $array)
	{
	}

	/**
	 * @param SomeAlias4|null $array
	 */
	public function withNullableArrayAlias(?array $array)
	{
	}

}

class Promoted
{

	public function __construct(
		/** @var array<int, string> */
		public array $promoted,
		/** @phpstan-var array<int, string> */
		public array $promoted2
	)
	{

	}

}
