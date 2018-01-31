<?php

/**
 * Hello world.
 *
 * @author Slevomat
 */
class Foo
{

	/**
	 * @param string $a
	 */
	public function __construct(string $a)
	{
	}

	/**
	 * Test.
	 */
	public function get(): int
	{
		return 0;
	}

	/**
	 * @@return int
	 *
	 * Not comment.
	 */
	public function notComment(): int
	{
		return 0;
	}

}
