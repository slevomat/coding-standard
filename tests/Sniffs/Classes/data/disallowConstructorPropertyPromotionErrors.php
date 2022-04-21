<?php // lint >= 8.1

class Whatever
{

	public function __construct(public int $a, private float $b)
	{

	}

}

class Anything
{

	public function __construct(
		public int $a,
		private float $b,
		readonly int $c,
	)
	{

	}

}
