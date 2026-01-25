<?php // lint >= 8.5

/**
 * @see mixed
 */
class Whatever
{

	/**
	 * @var
	 */
	private $invalidAnnotation;

	/**
	 * @var bool|int
	 */
	private $noMixed;

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
	 * @var mixed
	 */
	private $suppressed;

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
	 * @var array<mixed>
	 */
	#[Attribute1]
	public function foo(array $mixed)
	{
		return $mixed === true;
	}

	protected array $property;

	public readonly array $data;

}

class WhateverOverridden extends Whatever
{

	/**
	 * @var array<mixed>
	 */
	#[Override]
	protected array $property;

	/** @var array<string, mixed> */
	#[Override]
	public readonly array $data;

	/**
	 * @var array<mixed>
	 */
	#[\Override]
	public function foo(array $mixed)
	{
		return $mixed === false;
	}

}

/**
 * This MUST be the last docblock in the test file and there must not be any code
 * after this line for the test to be valid.
 */
