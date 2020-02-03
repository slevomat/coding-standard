<?php // lint >= 7.4

abstract class Foo {
	/** @var string */
	public static $staticFoo = 'bar'; // there may be a comment

	/** @var string */
	protected  static $staticBar = 'foo';

	/** @var int */
	private static $staticLvl = 666;

	// strange but yeah, whatever
	public $foo = 'bar';

	/** @var string */
	protected  $bar = 'foo';

	/** @var int */
	private $lvl = 9001;
	/**
	 * whatever
	 */
	public static abstract function wow();
	/**
	 * who cares
	 */
	private function such()
	{
	}
}

abstract class Bar {
	/** @var string */
	public static $staticFoo = 'bar'; // there may be a comment

	/** @var string */
	protected  static $staticBar = 'foo';

	/** @var int */
	private static $staticLvl = 666;

	// strange but yeah, whatever
	public $foo = 'bar';

	/** @var string */
	protected  $bar = 'foo';

	/** @var int */
	private $lvl = 9001;

	private $noComment = 'noComment';

	private int $int = 0;

	private string $string = '';


	/**
	 * whatever
	 */
	public static abstract function wow();
	/**
	 * who cares
	 */
	private function such()
	{
	}
}
