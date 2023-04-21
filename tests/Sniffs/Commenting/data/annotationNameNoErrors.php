<?php

/**
 * @deprecated
 *
 * @unknown-annotation
 */
class Whatever
{

	/**
	 * @param string $a
	 * @phpstan-return array{0: string, 1: int}
	 */
	public function method($a)
	{
	}

	/**
	 * @inheritDoc
	 *
	 * And some text with {@inheritDoc} annotation.
	 */
	public function inherit()
	{
	}

}
