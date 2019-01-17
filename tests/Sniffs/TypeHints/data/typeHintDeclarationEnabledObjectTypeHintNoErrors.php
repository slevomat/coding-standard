<?php

namespace FooNamespace;

abstract class FooClass
{

	abstract public function withNullableReturnTypeHint(): ?object;

	public function withNullableParameterTypeHint(?object $a): void
	{

	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
	 * @return object|null
	 */
	public function withReturnTypeHintSuppress()
	{
		return null;
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param object|null $a
	 */
	public function withParameterTypeHintSuppress($a): void
	{
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.UselessDocComment
	 * @param object|null $a
	 */
	public function withNullableParameterTypeHintAndSuppressedUselessDocComment(?object $a): void
	{
	}

	/**
	 * @return object[]|null
	 */
	public function returnsNullableArrayOfObjects(): ?array
	{
		return [];
	}

	/**
	 * @param \DateTimeImmutable $object
	 */
	public function parameterObjectTypeVariance(object $object): void
	{

	}

	/**
	 * @param \DateTimeImmutable|null $object
	 */
	public function parameterNullableObjectTypeVariance(?object $object): void
	{

	}

	/**
	 * @return \DateTimeImmutable
	 */
	public function returnsObjectTypeVariance(): object
	{

	}

	/**
	 * @return \DateTimeImmutable|null
	 */
	public function returnsNullableObjectTypeVariance(): ?object
	{

	}

}
