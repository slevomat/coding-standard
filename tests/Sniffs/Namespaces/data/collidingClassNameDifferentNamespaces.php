<?php

namespace Alpha;

use Bar\Foo;

class Beta {

	public function barFoo(): Foo
	{
		return new Foo();
	}

	public function bazFoo(): \Baz\Foo
	{
		return new \Baz\Foo();
	}

}
