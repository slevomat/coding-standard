<?php

namespace Whatever;

use F\Q\N;
use Some\SubNamespace;
use Some\SubNamespace\A;
use Some\SubNamespace\B;

class Foo
{

	public function test()
	{
		return SubNamespace::class;
	}

	public function test2()
	{
		/** @var A|B $test */
		$test = null;
	}

}
