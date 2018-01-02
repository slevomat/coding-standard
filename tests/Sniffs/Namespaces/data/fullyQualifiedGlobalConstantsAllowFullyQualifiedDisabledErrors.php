<?php

namespace Foo
{

    use const DOO;
    use const Name\Spaced;

	class Bar
	{
		public function __construct()
		{
			\FOO;
			\BAR\BAZ;
			DOO;
			Spaced;
		}
	}

}
