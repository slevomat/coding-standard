<?php

class Foo
{

	private $bar;
	private $foo;

	public function __construct(string $bar, string $foo)
	{
		$this->bar = $bar;
		$this->foo = $foo;
	}

	public function __toString(): string
	{
		return "foo{$this->bar}bar{$this->foo()}";
	}

}
