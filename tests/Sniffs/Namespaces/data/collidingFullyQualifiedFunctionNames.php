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
	}

}
