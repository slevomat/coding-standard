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
	 * @param int $a
	 *
	 * @return bool
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

	/**
	 * @first
	 * @second
	 */
	public function twoUndefinedAnnotations()
	{

	}

	/**
	 * @param int $a
	 *
	 * @phpstan-param int $a
	 * @phpstan-return bool
	 * @phpstan-whatever X
	 *
	 * @phpcs:disable
	 * @phpcs:enable
	 */
	public function phpstanAndPhpcsAnnotations($a)
	{
		return false;
	}

}
