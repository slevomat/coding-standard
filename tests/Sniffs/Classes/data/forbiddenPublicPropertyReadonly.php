<?php // lint >= 8.1

class Test
{
	var $var;
	public $pub;
	readonly public string $rps;
	public readonly string $prs;
	public $pub2;

	public function __construct(
		public int $promoted1,
		readonly public string $promoted2
	)
	{
	}
}

class Test2
{
	public function __construct(protected int $promoted1, readonly private string $promoted2)
	{
	}
}
