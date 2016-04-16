<?php

namespace Baz;

use Bar\Something;
use Foo\MyType;

class TestSomeClass
{

	/**
	 * @return null
	 */
	public function getCell()
	{
		/* @var $variable MyType */
		$variable = Something::create();
		return NULL;
	}

}
