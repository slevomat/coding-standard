<?php

namespace Foo
{
	use Qualified;
	use const Used\HIHI;

	interface Doo
	{

	}

	trait Baz
	{
		public function __construct()
		{
		}
	}

	class Boo implements Doo
	{
		use Baz {
			__construct as initTrait;
		}

		public function __construct()
		{
			\PHP_VERSION;
			\FullyQualified\HEHE;
			Qualified\HAHA;
			HIHI;
		}
	}

}
?><?php
namespace {

	\PHP_OS;

}
