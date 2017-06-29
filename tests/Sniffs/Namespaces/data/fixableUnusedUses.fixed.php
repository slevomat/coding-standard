<?php

namespace Foo;

use Framework;
use Framework\Subnamespace;
use NewNamespace\ObjectPrototype as NewObject;
use R\S;
use T;
use function LoremIpsum\UsedFunction;
use const LoremIpsum\USED_CONSTANT;
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
