<?php

class WithParenheses
{

}

interface WithoutParenheses
{

}

trait DifferentCase
{

}

class ALotOfWhitespace
{

}

class Errors
{

	public function parameterWithNotIterableTypeHint(bool $a): bool
	{
		return true;
	}

	public function withNotIterableReturnType(): bool
	{
		return false;
	}

}
