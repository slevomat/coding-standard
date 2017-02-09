<?php

/**
 * @author Slevomat
 * @see https://www.slevomat.cz
 * @Route("/", name="homepage")
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
	 * @see multiline()
	 * @return int
	 * @throws \Exception
	 */
	public function get(): int
	{
		return 0;
	}

	/**
	 *
	 * @throws \Throwable Text text text text
	 * text text text text text
	 * @throws \TypeError Text text
	 * text text text
	 *
	 */
	public function multiline()
	{

	}

	/**
	 * Description description description
	 *  description description
	 *
	 * @see https://www.slevomat.cz
	 */
	public function withDescription()
	{

	}

}
