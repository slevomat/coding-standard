<?php

namespace Foo
{

	use const Foo\A as AA;
	use const Foo\B;
	use const Foo\Doo\BAR;

	class X
	{
		public function __construct()
		{
			\Bar\C;
			AA;
			B;
			BAR;
		}
	}

}
