<?php

namespace Foo\Test\Bla;
use Foo\DateTime;
use Foo\ArrayObject;
use Foo\Something;
use Foo\Exception;
use Foo\Traversable;

class Bar
{

	/** @var \DateTimeImmutable|int|DateTime */
	private $property;

	/**
	 * @var DateTime
	 */
	private $property2;

	/**
	 * @ORM\Column \Foo\Anything
	 */
	public function noErrors()
	{
		/**@var*/
	}

	/**
	 * Description
	 *
	 * @param array|ArrayObject|iterable $iterable Parameter description
	 * @param \Boo\Anything $anything
	 * @return Something[] Return description
	 * @throws Exception|SomeException Throws description
	 */
	public function method()
	{
		/** @var Traversable|array $variable */

		/** @var BlaBla\Foo @variable2 */
	}

	/**
	 * @return Something
	 */
	public function method2()
	{
	}

}
