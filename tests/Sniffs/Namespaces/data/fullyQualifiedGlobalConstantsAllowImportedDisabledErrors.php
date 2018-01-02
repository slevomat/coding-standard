<?php

namespace Foo
{

    use const X;
    use const Y as Z;
	use const Foo\A as AA;
	use const Foo\B;
	use const Foo\Doo\BAR;

	class X
	{
		public function __construct()
		{
		    X;
		    Z;
			\Bar\C;
			AA;
			B;
			BAR;
		}
	}

}
