<?php

namespace App;

use function Foo\get;

class Example
{

	public function method(): mixed
	{
		$value = ${get()};

		return $value;
	}

}
