<?php

namespace Whatever;

class Foo
{

	public function test(): void
	{
		new \Some\SubNamespace\A();
		new \Some\SubNamespace\B();
	}
}
