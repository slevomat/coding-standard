<?php // lint >= 7.4

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

}
