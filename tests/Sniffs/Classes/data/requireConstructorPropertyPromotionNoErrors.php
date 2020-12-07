<?php // lint >= 8.0

abstract class Test {
	abstract public function __construct($x);
}

interface Test {
	public function __construct($x);
}

class Whatever
{

	/**
	 * No parameters.
	 */
	public function __construct()
	{
	}

	public function noConstructor()
	{
	}

}

class Something
{

	public function __construct($noProperty)
	{
	}

}

class Anything
{

	public function __construct(public $withPromotion, private string $withPromotionAndTypeHint, callable &$callable, ...$variadic)
	{
	}

}

class Nothing
{

	private $a = '';

	private $b;

	private $c;

	private $d;

	public function __construct($a, $c, $d)
	{
		$phpVersion = phpversion();

		$className = $this::class;

		$this->b = 'b';

		$this->a .= 'a';

		$this->c = $a;

		$this->d = $d + $c;
	}

}
