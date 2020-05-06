<?php

class Whatever
{

	public function __construct()
	{

	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.Commenting.UselessFunctionDocComment
	 */
	public function isSniffSuppressed()
	{

	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.Commenting.UselessFunctionDocComment.UselessDocComment
	 */
	public function isSniffCodeSuppressed()
	{

	}

	/**
	 * {@inheritdoc}
	 */
	public function hasInheritdocAnnotation()
	{

	}

	/**
	 * Description
	 */
	public function hasDescription()
	{

	}

	/**
	 * @return int Very pretty number
	 */
	public function withDescriptionInReturnAnnotation(): int
	{

	}

	/**
	 * @return int
	 */
	public function withoutReturnTypeHint()
	{

	}

	/**
	 * @return int[]
	 */
	public function withSimpleTraverableReturnTypeHint(): array
	{

	}

	/**
	 * @return int[]
	 */
	public function withSpecificTraverableReturnTypeHint(): \Traversable
	{

	}

	/**
	 * @return $this
	 */
	public function withThisInReturnAnnotation(): self
	{

	}

	/**
	 * @return \Generic<int, string>
	 */
	public function withGenericType(): \Generic
	{

	}

	/**
	 * @return \Closure():array|null
	 */
	public function withNullableCallable(): ?\Closure
	{

	}

	/**
	 * @return \Closure():array
	 */
	public function withCallable(): \Closure
	{

	}

	/**
	 * @return \ImplementedInterface|\SecondImplementedInterface
	 */
	public function moreTypes(): \SomeInterface
	{

	}

	/**
	 * @param int $a Very pretty number
	 * @return bool
	 */
	public function withDescriptionInParameterAnnotation(int $a): bool
	{
		return true;
	}

	/**
	 * @param int $a
	 * @return bool
	 */
	public function withoutParameterTypeHint($a): bool
	{
		return true;
	}

	/**
	 * @param int[] $a
	 * @return bool
	 */
	public function withSimpleTraverableParameterTypeHint(array $a): bool
	{
		return true;
	}

	/**
	 * @param int[] $a
	 * @return bool
	 */
	public function withSpecificTraverableParameterTypeHint(Traversable $a): bool
	{
		return true;
	}

	/**
	 * @param $this $a
	 * @return bool
	 */
	public function withThisInParameterAnnotation(self $a): bool
	{
		return true;
	}

	/**
	 * @see Anything
	 * @param int $a
	 * @return bool
	 */
	public function withAnotherAnnotation(int $a): bool
	{
		return true;
	}

	/**
	 * @param int $aaaaa
	 * @return bool
	 */
	public function wrongParameterNameInAnnotation(int $a): bool
	{
		return true;
	}

	/**
	 * @param [] $a
	 * @return []
	 */
	public function invalidAnnotation(array $a): array
	{
		return [];
	}

}
