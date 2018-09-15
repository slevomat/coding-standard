<?php

/**
 * @author Jaroslav Hanslík
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
	 * Multiline
	 * description
	 *
	 * @param bool $a
	 *
	 * @X\Boo Multiline
	 *    description
	 * @X\Foo(
	 *     a=Y::SOME,
	 *     b={
	 *         @Z(
	 *             code=123
	 *         )
	 *     }
	 * ) Description
	 * @XX
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
	 *
	 * @undefined
	 * @undefined
	 */
	public function anotherMethod()
	{

	}

}
