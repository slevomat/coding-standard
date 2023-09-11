<?php // lint >= 8.2

abstract class Foo {
	public const Foo = 'bar';
	const Bar = 'foo';

	public static abstract function wow();
	private function such()
	{
	}
}

abstract class Bar {
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

class Foobar {
	private const ARR = [
		1,
		2,
		3,
	];

	private const FOO = 3;


	private $property;
}

const GLOBAL_CONSTANT = 1;
const GLOBAL_CONSTANT_2 = 2;

class TestClass
{
	public const CONSTANT_1 = 'foo';

	use TestTrait;

	private const CONSTANT_2 = 'bar';
}

enum A: string
{
	public const X = [];

	case A = 'a';

	case B = 'b';
}
