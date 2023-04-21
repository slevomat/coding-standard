<?php

/**
 * @deprecated Yes, it's deprecated
 */
class Whatever
{

	/**
	 * @param string $a Some comment
	 * @phpstan-return array{0: string, 1: int}
	 */
	public function method($a)
	{
	}

	/**
	 * @inheritDoc
	 *
	 * And some text with {@inheritDoc} and {@inheritDoc} annotation.
	 */
	public function inherit()
	{
	}

	/** @inheritDoc */
	public function inherit2()
	{
	}

	/** {@inheritDoc} */
	public function inherit3()
	{
	}

}
