<?php // lint >= 8.2

class Whatever
{

	public ( Foo&Boo   )|Bar $intersection;

	public function method(( Foo&Boo)|Bar $intersection): (   Foo&Boo )|(  Bar&Baz   )
	{
	}

}
