<?php

namespace Foo\Test\Bla;

use Doctrine\ORM\Mapping\Column as DoctrineColumn;

class Bar extends \ObjectPrototype implements \Iterator
{

	public function bar()
	{
		new Lorem();
		$constant = \Some\ConstantClass::CONSTANT;
		new \Foo\SomeError();
		new \Some\CommonException();
		new \Exception();
		new \Nette\ObjectPrototype();
	}

	public function foo(\Doctrine\ORM\Mapping\Column $doctrineColumn): \Nette\ObjectPrototype
	{
		return new \Nette\ObjectPrototype();
	}

}
