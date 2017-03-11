<?php

class Foo
{

	private $bar;
	private $foo;
	private $hoo;

	public function __construct(string $bar, string $foo)
	{
		$this->bar = $bar;
		$this->foo = $foo;
		$this->hoo = 'hoo';
	}

	public function __toString(): string
	{
		return "foo{$this->bar}bar$this->foo" . "hoo\$this->hoo";
	}

}
