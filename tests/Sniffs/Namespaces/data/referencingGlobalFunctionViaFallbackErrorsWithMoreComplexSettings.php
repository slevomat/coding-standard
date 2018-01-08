<?php

namespace FooNamespace;

function php_version()
{
	return '6.0.0';
}

class Foo
{

	public function boo()
	{
		\php_version();
		\max(1, 3);
		min(1, 3);
	}

}
