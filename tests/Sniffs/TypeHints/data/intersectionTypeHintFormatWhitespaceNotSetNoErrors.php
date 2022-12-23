<?php // lint >= 8.1

class Whatever
{

	public \ArrayAccess $notIntersection;

	public \ArrayAccess&\Iterator $property;

	public function method(\ArrayAcess&  \Iterator $parameter): \ArrayAccess  &\Stringable
	{
	}

}
