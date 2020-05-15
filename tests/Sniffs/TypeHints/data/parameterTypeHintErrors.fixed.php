<?php // lint >= 7.2

class Whatever
{

	private function noTypeHintNoAnnotation($a)
	{

	}

	/**
	 * @param int[] $a
	 */
	public function arrayTypeHint(array $a): void
	{

	}

	/**
	 * @param int[]|null $a
	 */
	public function arrayTypeHintWithNull(?array $a): void
	{

	}

	/**
	 * @param array{foo: int} $a
	 */
	public function arrayShapeTypeHint(array $a): void
	{

	}

	/**
	 */
	public function twoTypeWithNull(?string $a): void
	{
	}

	/**
	 * @param int[]|\Traversable $a
	 */
	public function specificTraversable(\Traversable $a)
	{

	}

	/**
	 */
	public function variadic(string ...$a)
	{

	}

	/**
	 */
	public function reference(string &$a)
	{

	}

	/**
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
	public function callableParameter(\Closure $parameter): void
	{

	}

	/**
	 * @param \Traversable $a
	 */
	public function onlyTraversable(\Traversable $a)
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
	public function traversableIntersection(\Traversable $a)
	{
	}

	/**
	 * @param \Traversable&array[] $a
	 */
	public function traversableIntersectionDifferentOrder(\Traversable $a)
	{
	}

	/**
	 * @param null|\Traversable $a
	 */
	public function traversableNull(?\Traversable $a)
	{
	}

	/**
	 */
	public function objectParameter(object $a)
	{
	}

	/**
	 * @param array<string>|array<int> $a
	 */
	public function unionWithSameBase(array $a)
	{
	}

	/**
	 * @param array<string>|array<int>|array<bool> $a
	 */
	public function unionWithSameBaseAndMoreTypes(array $a)
	{
	}

	/**
	 * @param array<int>|bool[] $a
	 */
	public function unionWithSameBaseToo(array $a)
	{
	}

	/**
	 * @param array<string>|array<int>|array<bool>|null $a
	 */
	public function unionWithSameNullableBase(?array $a)
	{
	}

	/**
	 * @param ?int $a
	 */
	public function nullable(?int $a)
	{
	}

	/**
	 * @param mixed[]|array $a
	 */
	public function traversableArray(array $a)
	{
	}

	/***/
	public function oneLineDocComment(string $a)
	{
	}

	/** @param true $a */
	public function constTrue(bool $a)
	{
	}

	/** @param FALSE $a */
	public function constFalse(bool $a)
	{
	}

	/** @param 0 $a */
	public function constInteger(int $a)
	{
	}

	/** @param 0.0 $a */
	public function constFloat(float $a)
	{
	}

	/** @param 'foo' $a */
	public function constString(string $a)
	{
	}

	/** @param 'foo'|null $a */
	public function constNullableString(?string $a)
	{
	}

	/** @param 'foo'|'bar' $a */
	public function constUnionString(string $a)
	{
	}

	/** @param class-string $a */
	public function classString(string $a)
	{
	}

}
