<?php

namespace Foo
{

	use const FOO;
	use const BAR as BAZ;

	class Bar
	{
		public function __construct()
		{
			FOO();
			BAZ();
		}
	}

}
