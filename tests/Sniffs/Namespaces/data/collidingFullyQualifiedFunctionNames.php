<?php

namespace FooNamespace;

use function B\decode;

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

decode(\A\decode(''));
