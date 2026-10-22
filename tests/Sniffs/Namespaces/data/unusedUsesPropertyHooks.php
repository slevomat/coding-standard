<?php // lint >= 8.4

namespace App;

use function Foo\get;
use function Foo\set;

class Example
{

	private int $backing = 0;

	public int $value {
		get => get();
		set => $this->backing = set($value);
	}

}
