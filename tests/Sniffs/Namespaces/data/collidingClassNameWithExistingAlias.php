<?php

namespace TestNamespace;

use Foo\User;
use Bar\User as BarUser;

class Test {

	public function foo(): User
	{
		return new User();
	}

	public function bar(): \Bar\User
	{
		return new \Bar\User();
	}

}
