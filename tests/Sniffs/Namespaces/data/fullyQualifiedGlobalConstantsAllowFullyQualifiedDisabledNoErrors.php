<?php

namespace Foo
{

	use const FOO;
	use const BAR as BAZ;
	use const Boo\DOO;

	class Bar
	{
		public function __construct()
		{
			FOO;
			BAZ;
			DOO;
			\Fully\QUALIFIED;
		}
	}

}
