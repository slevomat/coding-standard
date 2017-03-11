<?php

class Foo
{

	public function __construct()
	{
	}

	private function foo(): string
	{
		return 'foo';
	}

	private function bar(): string
	{
		return 'bar';
	}

	public function __toString(): string
	{
		return "foo{\$this->foo()}bar{$this->bar()}";
	}

}
