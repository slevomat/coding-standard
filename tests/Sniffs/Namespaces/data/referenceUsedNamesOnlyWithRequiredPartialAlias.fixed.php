<?php

namespace Whatever;
use Some\SubNamespace as SubNamespace;

class Foo
{

	public function test(): void
	{
		new SubNamespace\A();
		new SubNamespace\B();
	}
}
