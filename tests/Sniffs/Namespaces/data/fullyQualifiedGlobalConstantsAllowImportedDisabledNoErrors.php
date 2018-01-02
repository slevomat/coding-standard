<?php

namespace Foo
{

    use const Name\SPACED;

	class X
	{
		public function __construct()
		{
			\FOO;
			\Bar\Baz\Bang;
			SPACED;
		}
	}

}
