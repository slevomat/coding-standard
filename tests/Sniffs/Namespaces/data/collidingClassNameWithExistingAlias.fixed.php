<?php

namespace TestNamespace;

use Foo\User;
use Bar\User as BarUser;

class Test {

	public function foo(): User
	{
		return new User();
	}

	public function bar(): BarUser
	{
		return new BarUser();
	}

}
