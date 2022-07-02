<?php

namespace Foo\Bar;

class ClassLineCountSniffTest
{
	public function __construct()
	{
	}

	public function __get($name)
	{
	}

	/**
	 *
	 */
	public function someMethod() : string
	{
		return 'foo bar';
	}
}
