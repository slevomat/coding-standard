<?php // lint >= 8.5

class Foo
{

	/** @var list<string> */
	public static private(set) array $staticPrivateSet;

	/** @var list<string> */
	static public(set) array $staticFirstPublicSet;

}
