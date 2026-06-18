<?php

namespace Whatever;

use F\Q\N;
use Some\SubNamespace as SubNamespace;

class Foo
{

	public function test()
	{
		return SubNamespace::class;
	}

	public function test2()
	{
		/** @var SubNamespace\A|SubNamespace\B $test */
		$test = null;
	}

}
