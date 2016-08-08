<?php

namespace Foo;

use First\Object;
use Framework;
use Framework\Subnamespace;
use My\Object as MyObject;
use NewNamespace\Object as NewObject;
use R\S;
use T;
use X;

class TestClass
{

	/**
	 * @Assert\NotBlank(groups={X::SOME_CONSTANT}
	 */
	public function test(S $s)
	{
		new \Test\Foo\Bar();
		$date = T::today();
		new Framework\FooObject();
		new Subnamespace\BarObject();

		return new NewObject();
	}

}
