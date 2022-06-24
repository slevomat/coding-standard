<?php // lint >= 8.1

class Whatever
{

	public int $zero;

	public readonly int $one;

	static public string|bool $two = false;

	public function __construct(public readonly Foo|Bar $three)
	{
	}

}
