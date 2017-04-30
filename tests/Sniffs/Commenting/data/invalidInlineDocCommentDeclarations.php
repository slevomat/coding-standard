<?php

class Foo
{

	/** @var string */
	private $foo;

	public function __construct()
	{
		/** @var $a string[] */
		$a = $this->get();

		/** @see https://www.slevomat.cz */
		$b = null;

		/** @var $c */
		$c = [];

		/** @var $d iterable|array|\Traversable Lorem ipsum */
		$d = [];
	}

	public function get()
	{
		$a = [];
		return $a;
	}

}
