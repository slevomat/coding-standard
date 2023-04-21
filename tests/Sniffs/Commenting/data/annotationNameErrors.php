<?php

/**
 * @Deprecated Yes, it's deprecated
 */
class Whatever
{

	/**
	 * @pArAm string $a Some comment
	 * @PHPSTAN-return array{0: string, 1: int}
	 */
	public function method($a)
	{
	}

	/**
	 * @inheritdoc
	 *
	 * And some text with {@inheritdoc} and {@INHERITDOC} annotation.
	 */
	public function inherit()
	{
	}

}
