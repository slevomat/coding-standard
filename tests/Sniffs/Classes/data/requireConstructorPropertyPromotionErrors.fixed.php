<?php // lint >= 8.0

class Whatever
{

	public function __construct(public string $a, protected int|null $b = 0, private ?bool $c = null)
	{
	}

}
