<?php

namespace Foo
{

	use function foo;
	use function bar as baz;
	use function name\spaced;

	class Bar
	{
		public function __construct()
		{
			foo();
			baz();
			spaced();
		}
	}

}
