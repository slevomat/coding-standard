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
