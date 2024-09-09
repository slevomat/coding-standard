<?php

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

}

class WhateverOverridden extends Whatever
{

	/**
	 * @var array<mixed>
	 */
	#[\Override]
	public function foo(array $mixed)
	{
		return $mixed === false;
	}

}
