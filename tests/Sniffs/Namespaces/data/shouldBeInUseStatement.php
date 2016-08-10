<?php

namespace Foo\Test\Bla;

class Bar
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

}
