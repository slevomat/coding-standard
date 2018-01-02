<?php

namespace Foo
{

	use function foo;
	use function bar as baz;

	class Bar
	{
		public function __construct()
		{
			foo();
			baz();
		}
	}

}
