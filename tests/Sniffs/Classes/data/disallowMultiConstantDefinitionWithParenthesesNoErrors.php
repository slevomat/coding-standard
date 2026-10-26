<?php // lint >= 8.5

namespace Foo;

const I = new Complex(0, 1);

class Bar
{

	public const ADD = static function (int $a, int $b): int {
		return $a + $b;
	};

}
