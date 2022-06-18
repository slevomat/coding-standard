<?php // lint >= 8.0

class Whatever
{

	private function noTypeHintNoAnnotation($a)
	{

	}

	/**
	 * @param int[] $a
	 */
	public function arrayTypeHint($a): void
	{

	}

	/**
	 * @param int[]|null $a
	 */
	public function arrayTypeHintWithNull($a): void
	{

	}

	/**
	 * @param array{foo: int} $a
	 */
	public function arrayShapeTypeHint($a): void
	{

	}

	/**
	 * @param string|null $a
	 */
	public function twoTypeWithNull($a): void
	{
	}

	/**
	 * @param int[]|\Traversable $a
	 */
	public function specificTraversable($a)
	{

	}

	/**
	 * @param string ...$a
	 */
	public function variadic(...$a)
	{

	}

	/**
	 * @param string $a
	 */
	public function reference(&$a)
	{

	}

	/**
	 * @param string $a
	 */
	public function uselessAnnotation(string $a)
	{

	}

	public function missingAnnotationForTraversable(array $a)
	{

	}

	/**
	 * @param array $a
	 */
	public function missingItemsSpecification(array $a)
	{

	}

	/**
	 * @param \Closure(): array $parameter
	 */
	public function callableParameter($parameter): void
	{

	}

	/**
	 * @param \Traversable $a
	 */
	public function onlyTraversable($a)
	{

	}

	/**
	 * @param array{array} $a
	 */
	public function arrayShapeWithoutItemsSpecification(array $a)
	{

	}

	/**
	 * @param \Generic<array> $a
	 */
	public function genericWithoutItemsSpecification(\Generic $a)
	{

	}

	/**
	 * @param array[]&\Traversable $a
	 */
	public function traversableIntersection($a)
	{
	}

	/**
	 * @param \Traversable&array[] $a
	 */
	public function traversableIntersectionDifferentOrder($a)
	{
	}

	/**
	 * @param null|\Traversable $a
	 */
	public function traversableNull($a)
	{
	}

	/**
	 * @param object $a
	 */
	public function objectParameter($a)
	{
	}

	/**
	 * @param array<string>|array<int> $a
	 */
	public function unionWithSameBase($a)
	{
	}

	/**
	 * @param array<string>|array<int>|array<bool> $a
	 */
	public function unionWithSameBaseAndMoreTypes($a)
	{
	}

	/**
	 * @param array<int>|bool[] $a
	 */
	public function unionWithSameBaseToo($a)
	{
	}

	/**
	 * @param array<string>|array<int>|array<bool>|null $a
	 */
	public function unionWithSameNullableBase($a)
	{
	}

	/**
	 * @param ?int $a
	 */
	public function nullable($a)
	{
	}

	/**
	 * @param mixed[]|array $a
	 */
	public function traversableArray($a)
	{
	}

	/** @param string $a */
	public function oneLineDocComment(string $a)
	{
	}

	/** @param true $a */
	public function constTrue($a)
	{
	}

	/** @param FALSE $a */
	public function constFalse($a)
	{
	}

	/** @param 0 $a */
	public function constInteger($a)
	{
	}

	/** @param 0.0 $a */
	public function constFloat($a)
	{
	}

	/** @param 'foo' $a */
	public function constString($a)
	{
	}

	/** @param 'foo'|null $a */
	public function constNullableString($a)
	{
	}

	/** @param 'foo'|'bar' $a */
	public function constUnionString($a)
	{
	}

	/** @param class-string $a */
	public function classString($a)
	{
	}

	/** @param mixed $a */
	public function mixedType($a)
	{
	}

	/** @param mixed|null $a */
	public function nullableMixedType($a)
	{
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.UselessAnnotation
	 * @param string[] $a
	 */
	public function uselessSuppressOfUselessAnnotation(array $a)
	{
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingTraversableTypeHintSpecification
	 * @param string[] $a
	 */
	public function uselessSuppressOfMissingTraversableTypeHintSpecification(array $a)
	{
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingAnyTypeHint
	 * @phpstan-param class-string $a
	 */
	public function uselessSuppressOfMissingAnyTypeHintWithTypeHint(string $a)
	{
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @phpstan-param class-string $a
	 */
	public function uselessSuppressOfMissingNativeTypeHintWithTypeHint(string $a)
	{
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param scalar $a
	 */
	public function uselessSuppressOfMissingNativeTypeHint($a)
	{
	}

	/**
	 * @param ?int $a
	 */
	public function uselessAnnotationWithShortNullable(?int $a)
	{
	}

	/** @param $a */
	private function noTypeHintNoAnnotationWithPhpdoc($a)
	{
	}

}
