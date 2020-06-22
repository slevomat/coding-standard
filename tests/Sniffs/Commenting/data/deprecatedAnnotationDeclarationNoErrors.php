<?php

declare(strict_types=1);

class Whatever
{

	/**
	 * @deprecated with description
	 */
	public const DEPRECATED_WITH_DESCRIPTION = 'deprecated';

	/**
	 * @deprecated useSomething else
	 */
	public function deprecatedMethodWithDescriptionIsNotReported()
	{
	}

	/**
	 * Should not report the plain word deprecated inside a docblock
	 */
	public function deprecatedWordShouldNotBeReported()
	{
	}

	/**
	 * Should not report the @deprecated plain word deprecated inside a docblock
	 */
	public function deprecatedInDocblock()
	{
	}

	public function deprecatedVariable()
	{
		/** @deprecated This var is deprecated */
		$var = 2;
	}

	/**
	 * @see something
	 * @param string $var
	 * @return void
	 */
	public function functionWithNoDeprecatedShouldNotBeReported(string $var): void
	{
	}

}
