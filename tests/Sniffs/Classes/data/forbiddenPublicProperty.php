<?php // lint >= 8.4

class TestSniff
{
	use TestTrait {
		doSomething as public;
		doSomethingElse as public;
	}

	var $var;
	public $pub;
}

class Test
{
	var $var;
	public $pub;
	protected $prot;
	private $priv;
	public(set) string $pubSet;

	public protected(set) int $protectedSet;
	public private(set) int $privateSet;
}
