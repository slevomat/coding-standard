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
	 * @deprecated
	 */
	public function deprecatedWithoutDescriptionIsReported()
	{
	}

	public function deprecatedVariable()
	{
		/** @deprecated */
		$var = 1;
	}

}
