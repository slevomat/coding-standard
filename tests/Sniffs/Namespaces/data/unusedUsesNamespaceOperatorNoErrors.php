<?php

namespace Foo;

use Bar;

class MyClass {
	function foobar() {
		namespace\callMe();
		if (method_exists( Bar::class, 'method')) {}
	}
}
