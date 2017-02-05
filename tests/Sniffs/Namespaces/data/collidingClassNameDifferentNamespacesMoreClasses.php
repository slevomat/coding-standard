<?php

namespace Alpha;

class Beta {

	public function barFoo(): \Bar\Foo
	{
		return new \Bar\Foo();
	}

	public function bazFoo(): \Baz\Foo
	{
		return new \Baz\Foo();
	}

}

class Foo {

	public function alphaFoo(): Foo
	{
		return new Foo();
	}

}
