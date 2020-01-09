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
	 * @throws \Exception
	 * @X\Foo(
	 *     a=Y::SOME,
	 *     b={
	 *         @Z(
	 *             code=123
	 *         )
	 *     }
	 * ) Description
	 * @XX
	 * @X\Boo MultiLine
	 *    description
	 *
	 *
	 * @param bool $a
	 */
	public function method()
	{

	}

	/**
	 * Another method.
	 *
	 * @undefined
	 *
	 * @whatever
	 *
	 * @link https://github.com/slevomat/coding-standard
	 * @todo Make things happen.
	 * @link https://github.com/slevomat/coding-standard
	 *
	 * @anything
	 *
	 * @undefined
	 */
	public function anotherMethod()
	{

	}

	/**
	 * @param int $a
	 * @param int|null $b
	 * @param string $c
	 *
	 * @dataProvider oneMoreMethodData
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

	/** @return bool
	 * @param int $a
	 */
	public function methodWithInvalidDocComment($a): bool
	{

	}

	/**
	 * @first
	 *
	 * @second
	 */
	public function twoUndefinedAnnotations()
	{

	}

	/**
	 * @phpstan-whatever X
	 * @phpcs:disable
	 * @phpstan-param int $a
	 * @phpcs:enable
	 * @phpstan-return bool
	 *
	 * @param int $a
	 */
	public function phpstanAndPhpcsAnnotations($a)
	{
		return false;
	}

}
