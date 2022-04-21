<?php // lint >= 8.1

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


	final const FINAL_CONSTANT = 'final';


	final public const FINAL_PUBLIC_CONSTANT = 'final';


	public final const PUBLIC_FINAL_CONSTANT = 'final';


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
