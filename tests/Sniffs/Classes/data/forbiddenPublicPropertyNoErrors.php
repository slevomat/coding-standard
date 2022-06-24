<?php // lint >= 8.1

function add(int $a, int $b)
{
	return $a + $b;
}

class Test
{
	private $one, $two;

	public function __construct(public int $promoted)
	{
	}
}

class TestSniff
{
	private $one, $two;
}
