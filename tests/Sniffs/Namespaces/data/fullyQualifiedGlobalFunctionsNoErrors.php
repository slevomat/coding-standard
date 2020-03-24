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
			\min(10, 100);
			\FullyQualified\hehe();
			Qualified\haha();
			hihi();
		}
	}

}
?><?php
namespace {

	\MAX(10, 100);

}
