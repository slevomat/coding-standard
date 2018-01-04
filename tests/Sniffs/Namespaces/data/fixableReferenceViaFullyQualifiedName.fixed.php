<?php

namespace Foo\Test\Bla;

use Doctrine\ORM\Mapping\Column as DoctrineColumn;
use Iterator;
use Some\ConstantClass;
use Foo\SomeError;
use Nette\ObjectPrototype;
use function Boo\foo;
use const Boo\FOO;

class Bar extends \ObjectPrototype implements Iterator
{

	public function bar()
	{
		new Lorem();
		$constant = ConstantClass::CONSTANT;
		new SomeError();
		new \Some\CommonException();
		new \Exception();
		new ObjectPrototype();
		foo();
		FOO;
	}

	public function foo(DoctrineColumn $doctrineColumn): ObjectPrototype
	{
		return new ObjectPrototype();
	}

}
