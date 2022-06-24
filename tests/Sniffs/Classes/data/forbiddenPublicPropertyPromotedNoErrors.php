<?php // lint >= 8.1

class Test
{
	public function __construct(protected int $promoted1, readonly private string $promoted2)
	{
	}
}
