<?php

namespace FooNamespace;

use const SOMETHING;

const PHP_VERSION = '6.0.0';

class Foo
{

	public function boo()
	{
		echo \PHP_VERSION .  SOMETHING . \A\SOMETHING;
	}

}
