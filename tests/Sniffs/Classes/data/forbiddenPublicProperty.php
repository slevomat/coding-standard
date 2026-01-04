<?php // lint >= 8.5

class TestSniff
{
	use TestTrait {
		doSomething as public;
		doSomethingElse as public;
	}

	var $var;
	public $pub;
}

class ParentClass
{

	public $fromParent;

}

class Test extends ParentClass
{
	var $var;
	public $pub;
	protected $prot;
	private $priv;
	public(set) string $pubSet;

	public protected(set) int $protectedSet;
	public private(set) int $privateSet;

	#[Override]
	public $fromParent;

}
