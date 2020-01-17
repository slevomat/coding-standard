<?php

abstract class Foo {
	public static $staticFoo = 'bar';
	protected  static $staticBar = 'foo';
	private static $staticLvl = 666;
	public $foo = 'bar'; // there may be a comment
	protected  $bar = 'foo';
	private $lvl = 9001;

	public static abstract function wow();
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
