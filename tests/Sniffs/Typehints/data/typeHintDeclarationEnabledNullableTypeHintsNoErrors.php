<?php // lint >= 7.1

namespace FooNamespace;

abstract class FooClass
{

	abstract public function withNullableReturnTypeHint(): ?string;

	/**
	 * @phpcsSuppress SlevomatCodingStandard.Typehints.TypeHintDeclaration.MissingReturnTypeHint
	 * @return string|null
	 */
	public function withSuppress()
	{
		return '';
	}

}
