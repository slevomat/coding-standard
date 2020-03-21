<?php

abstract class Foo {
	/** @var string */
	public const Foo = 'bar';
	/** @var string */
	const Bar = 'foo';
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
	public const Foo = 'bar';


	/** @var string */
	const Bar = 'foo';



	const WHATEVER = 'whatever';



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
