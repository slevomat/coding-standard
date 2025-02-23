<?php // lint >= 8.2

class Whatever
{

	public Bar| ( Foo & Boo   ) $intersection;

	public function method(( Foo &Boo)|Bar $intersection): (   Foo&Boo ) | (  Bar&Baz   )
	{
	}

}
