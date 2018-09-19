<?php

/**
 * @author Jaroslav Hanslík
 * @see https://github.com/slevomat/coding-standard
 */
class Whatever
{

	/**
	 * Description
	 *
	 * @var string
	 */
	private $property;

	/**
	 * MultiLine
	 * description
	 *
	 * @param bool $a
	 *
	 * @X\Boo MultiLine
	 *    description
	 * @X\Foo(
	 *     a=Y::SOME,
	 *     b={
	 *         @Z(
	 *             code=123
	 *         )
	 *     }
	 * ) Description
	 *
	 * @throws \Exception
	 */
	public function method()
	{

	}

	/**
	 * Another method.
	 *
	 * @link https://github.com/slevomat/coding-standard
	 * @link https://github.com/slevomat/coding-standard
	 * @todo Make things happen.
	 *
	 * @whatever
	 *
	 * @anything
	 */
	public function anotherMethod()
	{

	}

	/**
	 * @param string $a
	 *
	 * @anything
	 */
	public function oneMoreMethod($a)
	{

	}

}
