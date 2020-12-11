<?php // lint >= 8.0

class Foo
{

	use Whatever {
		doSomething as public;
	}

	private $boo = 'boo';

	public string $booo = 'booo';

	protected array $array = [
		'a',
		'b',
		'c',
	];

	public function __construct(private string $propertyPromotion, private int $propertyPromotion2)
	{
	}

	public function anything()
	{

	}

}
