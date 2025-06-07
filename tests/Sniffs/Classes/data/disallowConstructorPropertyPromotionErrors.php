<?php // lint >= 8.4

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
		protected(set) bool $d,
		private(set) bool $e,
	)
	{

	}

}
