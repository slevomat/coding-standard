<?php

namespace Whatever;

use Some\SubNamespace as SubNamespace;
use SomeFramework;

class Foo
{

	#[SubNamespace\A]
	#[SubNamespace\B]
	public function test(): void
	{
		new SubNamespace\A();
		new SubNamespace\B();
		new SomeFramework\ObjectPrototype();
	}
}
