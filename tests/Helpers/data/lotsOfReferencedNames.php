<?php

namespace FooNamespace;

use Doctrine\ORM\Mapping as ORM;
use UsedNamespace\UsedNameFooBar as UsedNameFooBarBaz;

class FooClass extends \ExtendedClass implements \ImplementedInterface
{

	/** @ORM\Column(name="foo") */
	private $foo;

	/** @var Bar */
	private $bar;

	/**
	 * @param TypehintedName $foo
	 * @param AnotherTypehintedName[] $bar
	 */
	public function fooMethod(TypehintedName $foo, array $bar)
	{
		try {
			$var = new ClassInstance();
			$var->objectMethod();
			StaticClass::staticMethod();
			throw new \Foo\Bar\SpecificException();
		} catch (\Foo\Bar\Baz\SomeOtherException $e) {
			throw $e;
		}
	}

}
