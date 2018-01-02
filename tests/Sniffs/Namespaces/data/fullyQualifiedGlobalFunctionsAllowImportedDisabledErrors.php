<?php

namespace Foo
{

	use function Foo\a as aa;
	use function Foo\b;
	use function Foo\bar as BAZ;

	class X
	{
		public function __construct()
		{
			\Bar\c();
			aa();
			b();
			BAZ();
			baz();
		}

	}

}
