<?php // lint >= 8.1

class Whatever
{

	public \ArrayAccess&\Traversable $property;

	public function method(\ArrayAccess&\Traversable $parameter): \ArrayAccess&\Traversable&\Stringable
	{
	}

}
