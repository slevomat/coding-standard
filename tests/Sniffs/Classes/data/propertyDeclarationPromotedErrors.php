<?php // lint >= 8.1

class Whatever
{

	public function __construct(private  int  $promotion1, readonly public  int $promotion2)
	{
	}

}

class Anything
{

	public function __construct(
		private  int  $promotion3,
		readonly protected Foo|Bar|null $promotion4,
	)
	{
	}

}
