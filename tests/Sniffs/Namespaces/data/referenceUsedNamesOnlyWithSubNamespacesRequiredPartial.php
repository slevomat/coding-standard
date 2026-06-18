<?php

namespace Whatever;

use F\Q\N;

class Foo
{

	public function test()
	{
		return \Some\SubNamespace::class;
	}

	public function test2()
	{
		/** @var \Some\SubNamespace\A|\Some\SubNamespace\B $test */
		$test = null;
	}

}
