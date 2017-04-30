<?php

class Foo
{

	/** @var string */
	private $foo;

	public function __construct()
	{
		/** @var string[] $a */
		$a = $this->get();

		/** @see https://www.slevomat.cz */
		$b = null;

		/** @var iterable|array|\Traversable $d Lorem ipsum */
		$d = [];
	}

	public function get()
	{
		$a = [];
		return $a;
	}

}
