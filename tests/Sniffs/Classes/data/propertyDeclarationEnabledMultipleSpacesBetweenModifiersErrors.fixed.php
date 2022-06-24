<?php // lint >= 8.1

class Whatever
{

	public readonly int $one;

	static public string|bool $two = false;

	public function __construct(public readonly ?Foo $three, readonly public Foo|Bar|null $four)
	{
	}

}

class Anything
{

	public function __construct(
		public readonly ?Foo $five,
		readonly protected Foo|Bar|null $six,
	)
	{
	}

}
