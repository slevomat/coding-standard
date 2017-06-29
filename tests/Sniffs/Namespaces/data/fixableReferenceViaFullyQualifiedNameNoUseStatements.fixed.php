<?php

namespace Foo\Test\Bla;
use Nette\ObjectPrototype;

class Bar extends \ObjectPrototype implements \Iterator
{

	public function bar()
	{
		new ObjectPrototype();
	}

}
