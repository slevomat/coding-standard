<?php // lint >= 8.0

class Whatever
{

	use Anything {
		doSomething as public;
	}

	public const WHATEVER = 0;

	public function whatever()
	{
	}

	private $withoutTypeHint = 'false';

	private ?string $nullable = 'string';

	private int $notNullable = 0;

	private int|float $union = 0;

	private null|float $nullableUnion = 0.0;

	private false|int $unionWithFalse = false;

}
