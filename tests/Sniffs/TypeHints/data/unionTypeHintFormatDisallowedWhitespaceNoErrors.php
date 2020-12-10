<?php // lint >= 8.0

class Whatever
{

	public int $notUnion;

	public int|string $property;

	public function method(int|false $parameter): int|string
	{
	}

}
