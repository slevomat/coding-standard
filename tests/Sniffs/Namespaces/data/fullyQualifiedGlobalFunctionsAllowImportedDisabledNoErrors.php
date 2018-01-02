<?php

namespace Foo
{

	class X
	{
		public function __construct()
		{
			\foo\bar();
			\Bar\Baz\c();
			\foo\bar\baz();
		}
	}

}
