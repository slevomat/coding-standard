<?php

declare(strict_types=1);

/**
 * @deprecated
 */
class Whatever
{
	/**
	 * @deprecated
	 */
	public const DEPRECATED_WITHOUT = 'deprecated';

	/**
	 * @deprecated with description
	 */
	public const DEPRECATED_WITH = 'deprecated';

	/**
	 * @deprecated useSomething else
	 */
	public function deprecatedMethodWithDescriptionIsNotReported()
	{
	}

	/**
	 * @deprecated
	 */
	public function deprecatedWithoutDescriptionIsReported()
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
		/** @deprecated */
		$var1 = 1;

		/** @deprecated This var is deprecated */
		$var2 = 2;
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
