<?php // lint >= 8.2

class Whatever
{

	public int $notUnion;

	public int|string $union;

	public Foo&Boo $intersection;

	public function method(int|  false $union): int  |string
	{
	}

	public function method2(Foo & Boo $intersection): Foo&  Boo
	{
	}

}
