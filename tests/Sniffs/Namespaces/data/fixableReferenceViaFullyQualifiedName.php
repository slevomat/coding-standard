<?php

namespace Foo\Test\Bla;

class Bar extends \Object implements \Iterator
{

	public function bar()
	{
		new Lorem();
		$constant = \Some\ConstantClass::CONSTANT;
		new \Foo\SomeError();
		new \Some\CommonException();
		new \Exception();
		new \Nette\Object();
	}

	public function foo(): \Nette\Object
	{
		return new \Nette\Object();
	}

}
