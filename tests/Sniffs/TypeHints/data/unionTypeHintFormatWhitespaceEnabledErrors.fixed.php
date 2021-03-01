<?php // lint >= 8.0

class Whatever
{

	public int | string $property;

	public function method(int | false $parameter, string | false $parameter2): int | string | bool
	{
	}

}
