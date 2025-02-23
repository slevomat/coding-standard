<?php // lint >= 8.0

class Whatever
{

	public ?int $property;

	public int|float|null $shortNotPossible;

	public function method(?bool $parameter): ?string
	{
	}

}
