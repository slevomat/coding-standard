<?php // lint >= 8.1

class Test
{
	public function __construct(public int $promoted1, readonly public string $promoted2)
	{
	}
}

class Test2
{
	public function __construct(
		public int $promoted3,
		readonly public string $promoted4,
	)
	{
	}
}
