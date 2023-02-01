<?php // lint >= 8.2

enum Foo: string {
	/** @var string */
	case BAR = 'bar';
	/** @var string */
	case FOO = 'foo';
	/**
	 * whatever
	 */
	public static function wow()
	{
	}
	/**
	 * who cares
	 */
	private function such()
	{
	}
}

enum Bar: string {
	/** @var string */
	case BAR = 'bar';


	/** @var string */
	case FOO = 'foo';



	case WHATEVER = 'whatever';


	final const FINAL_CONSTANT = 'final';


	/**
	 * whatever
	 */
	public static function wow()
	{
	}
	/**
	 * who cares
	 */
	private function such()
	{
	}
}

enum Boo
{

	case BOO;


	case BOOO;

}
