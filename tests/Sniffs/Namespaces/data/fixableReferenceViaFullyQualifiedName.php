<?php

namespace Foo\Test\Bla;

use Doctrine\ORM\Mapping\Column as DoctrineColumn;

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

	public function foo(\Doctrine\ORM\Mapping\Column $doctrineColumn): \Nette\Object
	{
		return new \Nette\Object();
	}

}
