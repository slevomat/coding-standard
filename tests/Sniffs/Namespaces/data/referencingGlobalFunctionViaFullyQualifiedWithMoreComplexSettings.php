<?php

namespace FooNamespace;

use function phpversion;

class Foo
{

	public function boo()
	{
		phpversion();
		\phpversion();
	}

}
