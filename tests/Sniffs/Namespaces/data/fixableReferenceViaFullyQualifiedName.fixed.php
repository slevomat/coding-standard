<?php

namespace Foo\Test\Bla;

use Doctrine\ORM\Mapping\Column as DoctrineColumn;
use Iterator;
use Some\ConstantClass;
use Foo\SomeError;
use Nette\Object;

class Bar extends \Object implements Iterator
{

	public function bar()
	{
		new Lorem();
		$constant = ConstantClass::CONSTANT;
		new SomeError();
		new \Some\CommonException();
		new \Exception();
		new Object();
	}

	public function foo(DoctrineColumn $doctrineColumn): Object
	{
		return new Object();
	}

}
