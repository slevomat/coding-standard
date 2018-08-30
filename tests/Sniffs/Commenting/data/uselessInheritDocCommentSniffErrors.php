<?php

/**
 * {@inheritdoc}
 */
class WithParenheses
{

}

/**
 * @inheritdoc
 */
interface WithoutParenheses
{

}

/**
 * {@InHeRiTdOc}
 */
trait DifferentCase
{

}

/**
 *
 *         {@InHeRiTdOc}
 *
 *
 */
class ALotOfWhitespace
{

}

class Errors
{

	/**
	 * {@inheritdoc}
	 */
	public function parameterWithNotIterableTypeHint(bool $a): bool
	{
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function withNotIterableReturnType(): bool
	{
		return false;
	}

}
