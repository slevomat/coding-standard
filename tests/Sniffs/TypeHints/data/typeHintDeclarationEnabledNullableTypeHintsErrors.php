<?php

namespace FooNamespace;

use AnyNamespace\Anything;

abstract class FooClass
{

	/**
	 * @return string|null
	 */
	public function withNullableReturnTypeHint()
	{
		return 'string';
	}

	/**
	 * @return string|null
	 */
	abstract public function abstractWithNullableReturnTypeHint();

	/**
	 * @return int|null
	 */
	abstract public function abstractWithNullableReturnTypeHintAndUselessDocComment(): ?int;

	/**
	 * @return string|null
	 */
	public function withNullableReturnTypeHintAndUselessDocComment(): ?string
	{
		return 'string';
	}

	/**
	 * @param string|null $a
	 */
	public function withNullableParameterTypeHint($a)
	{
	}

	/**
	 * @param int|null $a
	 */
	public function withNullableParameterTypeHintAndUselessDocComment(?int $a)
	{
	}

	/**
	 * @return string[]|null
	 */
	public function returnsNullableArrayOfStrings()
	{
		return [];
	}

	/**
	 * @param string[]|null $a
	 */
	public function parameterNullableArrayOfStrings($a)
	{

	}

	/**
	 * @return \Something|null
	 */
	public function returnTypeHintEqualsAnnotationBothFullyQualified(): ?\Something
	{
		return new \Something();
	}

	/**
	 * @return null|\AnyNamespace\Anything
	 */
	public function returnTypeHintEqualsAnnotationWithOnlyAnnotationFullyQualified(): ?Anything
	{
		return new Anything();
	}

	/**
	 * @return Anything|null
	 */
	public function returnTypeHintEqualsAnnotationWithOnlyTypeHintFullyQualified(): ?\AnyNamespace\Anything
	{
		return new Anything();
	}

	/**
	 * @param \Something|null $a
	 */
	public function parameterTypeHintEqualsAnnotationBothFullyQualified(?\Something $a)
	{
	}

	/**
	 * @param null|\AnyNamespace\Anything $a
	 */
	public function parameterTypeHintEqualsAnnotationWithOnlyAnnotationFullyQualified(?Anything $a)
	{
	}

	/**
	 * @param Anything|null $a
	 */
	public function parameterTypeHintEqualsAnnotationWithOnlyTypeHintFullyQualified(?\AnyNamespace\Anything $a)
	{
	}

}
