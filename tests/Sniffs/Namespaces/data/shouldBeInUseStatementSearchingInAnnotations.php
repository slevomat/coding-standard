<?php

namespace Foo\Test\Bla;

class Bar
{

	/** @var \DateTimeImmutable|int|\Foo\DateTime */
	private $property;

	/**
	 * @var \Foo\DateTime
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
	 * @param array|\Foo\ArrayObject|iterable $iterable Parameter description
	 * @param \Boo\Anything $anything
	 * @return \Foo\Something[] Return description
	 * @throws \Foo\Exception|SomeException Throws description
	 */
	public function method()
	{
		/** @var \Foo\Traversable|array $variable */

		/** @var BlaBla\Foo @variable2 */
	}

	/**
	 * @return \Foo\Something
	 */
	public function method2()
	{
	}

}
