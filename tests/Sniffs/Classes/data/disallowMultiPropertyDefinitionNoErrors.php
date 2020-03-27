<?php // lint >= 7.4

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

	public function anything()
	{

	}

}
