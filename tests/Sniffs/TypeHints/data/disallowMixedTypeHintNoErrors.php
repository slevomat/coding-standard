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

}
