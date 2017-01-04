<?php // lint >= 7.1

namespace FooNamespace;

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

}
