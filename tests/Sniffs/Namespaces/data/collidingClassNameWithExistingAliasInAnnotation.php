<?php

namespace TestNamespace;

use Foo\User;
use Bar\User as BarUser;

class Test {

	/**
	 * @var User|\Bar\User|null
	 */
	public $prop;

}
