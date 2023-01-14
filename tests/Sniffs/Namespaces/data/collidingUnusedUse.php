<?php

namespace Alpha;

use \Bar\Foo\Bar;
use Baz;

class Beta {

	/**
	 * @param \Bar\Foo\Bar $foo Some parameter.
	 * @return bool|Baz Some return.
	 */
	public function barFoo(Bar $foo)
	{
		return $foo instanceof Bar;
	}
}
