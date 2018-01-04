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
	}

}
