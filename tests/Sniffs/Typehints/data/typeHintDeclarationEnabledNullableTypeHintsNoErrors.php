<?php // lint >= 7.1

namespace FooNamespace;

abstract class FooClass
{

	abstract public function withNullableReturnTypeHint(): ?string;

	public function withNullableParameterTypeHint(?string $a)
	{

	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.Typehints.TypeHintDeclaration.MissingReturnTypeHint
	 * @return string|null
	 */
	public function withSuppress()
	{
		return '';
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.Typehints.TypeHintDeclaration.UselessDocComment
	 * @param int|null $a
	 */
	public function withNullableParameterTypeHintAndSuppressedUselessDocComment(?int $a)
	{
	}

	/**
	 * @return string[]|null
	 */
	public function returnsNullableArrayOfStrings(): ?array
	{
		return [];
	}

	/**
	 * @return resource|null
	 */
	public function returnsNullableResource()
	{
		return null;
	}

	/**
	 * @return mixed|null
	 */
	public function returnsNullableMixed()
	{
		return null;
	}

}
