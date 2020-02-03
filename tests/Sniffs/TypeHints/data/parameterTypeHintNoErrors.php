<?php

use Doctrine\Common\Collections\ArrayCollection;
use SlevomatCodingStandard\Helpers\ParameterTypeHint;
use SlevomatCodingStandard\Helpers\PropertyTypeHint;
use SlevomatCodingStandard\Helpers\ReturnTypeHint;

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
	 * @param mixed $a
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
	 * @param $a
	 */
	public function invalidAnnotation(int $a)
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

}
