<?php // lint >= 8.1

class Whatever
{

	public function __construct(private int $promotion1, public readonly int $promotion2)
	{
	}

}

class Anything
{

	public function __construct(
		private int $promotion3,
		public readonly Foo|Bar|null $promotion4,
	)
	{
	}

}
