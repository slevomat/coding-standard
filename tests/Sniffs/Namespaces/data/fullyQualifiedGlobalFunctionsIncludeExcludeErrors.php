<?php

namespace Foo
{
	use Qualified;
	use function Used\hihi;

	interface Doo
	{

	}

	class Boo implements Doo
	{
		public function __construct()
		{
			mIn(10, 100);
			\FullyQualified\hehe();
			Qualified\haha();
			hihi();
		}
	}

}

namespace {

	max(10, 100);

}
