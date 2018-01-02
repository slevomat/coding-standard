<?php

namespace Foo
{

	use function a as aa;
	use function b;
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
