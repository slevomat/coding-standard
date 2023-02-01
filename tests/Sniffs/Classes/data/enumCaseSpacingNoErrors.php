<?php // lint >= 8.1

enum Foo: string {
	case BAR = 'bar';
	case FOO = 'foo';

	public static function wow()
	{

	}

	private function such()
	{
	}
}

enum Bar: string {
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

enum TestClass
{
	case FOO;

	use TestTrait;

	case BAR;
}
