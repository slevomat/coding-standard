<?php

namespace Foo;

use First\ObjectPrototype;
use Framework;
use Framework\Subnamespace;
use My\ObjectPrototype as MyObject;
use NewNamespace\ObjectPrototype as NewObject;
use R\S;
use T;
use Test_Invalid;
use function FooBar\UnusedFunction;
use function LoremIpsum\UsedFunction;
use const FooBar\UNUSED_CONSTANT;
use const LoremIpsum\USED_CONSTANT;
use X;
use Lorem\FirstInterface;
use Ipsum\SecondInterface;
use Zetta\Rasmus;

class TestClass implements FirstInterface, SecondInterface
{

	/**
	 * @Assert\NotBlank(groups={X::SOME_CONSTANT}
	 */
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

		return new NewObject();
	}

	/**
	 * @return Test_Invalid
	 */
	protected function getCell()
	{
		return NULL;
	}

}
