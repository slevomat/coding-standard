<?php

namespace Foo\Test\Bla;

use Doctrine\ORM\Mapping\Column as DoctrineColumn;
use Iterator;
use Some\ConstantClass;
use Foo\SomeError;
use Nette\ObjectPrototype;
use function Boo\foo;
use const Boo\FOO;
use function min;
use const PHP_VERSION;

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
		min(1, 2);
		PHP_VERSION;
	}

	public function foo(DoctrineColumn $doctrineColumn): ObjectPrototype
	{
		return new ObjectPrototype();
	}

}
