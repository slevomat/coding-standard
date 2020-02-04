<?php // lint >= 7.4

class Foo
{

	/**
	 * @var bool
	 */
	public $boolean = true;

	private $string = 'string';

	public function __construct($boo)
	{
		$hoo = $boo;
	}

	private$weirdDefinition;

	private ?\Whatever\Anything $withTypeHint;

	private int $withSimpleTypeHint;

	public function whatever()
	{

	}

	private string $typedPropertyAfterMethod;

}
