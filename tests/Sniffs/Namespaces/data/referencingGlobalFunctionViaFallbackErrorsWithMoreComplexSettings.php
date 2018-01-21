<?php

namespace FooNamespace;

function phpversion()
{
	return '6.0.0';
}

class Foo
{

	public function boo()
	{
		\phpversion();
		\max(1, 3);
		min(1, 3);
	}

}
