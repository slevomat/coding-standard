<?php

namespace Foo;

use First\ObjectPrototype;
use Framework;
use Framework\Subnamespace;
use My\ObjectPrototype as MyObject;
use NewNamespace\ObjectPrototype as NewObject;
use R\S;
use T;
use function FooBar\UnusedFunction;
use function LoremIpsum\UsedFunction;
use const FooBar\UNUSED_CONSTANT;
use const LoremIpsum\USED_CONSTANT;
use X;
use Lorem\FirstInterface;
use Ipsum\SecondInterface;
use Zetta\Rasmus;
use My\PartialClass;
use My\PartialFunction;
use My\PartialConstant;
use PartialNamespace;

class TestClass implements FirstInterface, SecondInterface
{

	public function test(S $s): Rasmus
	{
		new \Test\Foo\Bar();
		$date = T::today();
		new Framework\FooObject();
		new Subnamespace\BarObject();

		$functionNameAsClass = new UnusedFunction();
		$unusedConstant = new UNUSED_CONSTANT();
		UsedFunction();
		doFoo(USED_CONSTANT);

		new PartialClass\UsedClass();
		PartialFunction\usedFunction();
		PartialConstant\USED_CONSTANT;
		new PartialNamespace\UsedClass();

		return new NewObject();
	}

}
