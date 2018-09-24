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

	/**
	 * @dataProvider oneMoreMethodData
	 *
	 * @param int $a
	 * @param int|null $b
	 * @param string $c
	 */
	public function oneMoreMethod($a, $b, $c)
	{

	}

	/**
	 * @return bool
	 *
	 * @param int $a
	 */
	public function methodBeforeInvalidDocComment($a): bool
	{

	}

	/**
	 * @param int $a
	 *
	 * @return bool
	 */
	public function methodWithInvalidDocComment($a): bool
	{

	}

}
