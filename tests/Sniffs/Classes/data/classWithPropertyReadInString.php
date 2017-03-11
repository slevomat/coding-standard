<?php

class Foo
{

	private $bar;

	public function __construct(string $bar)
	{
		$this->bar = $bar;
	}

	public function __toString(): string
	{
		return "foo{$this->bar}";
	}

}
