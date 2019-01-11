<?php

namespace FooNamespace;

use DateTimeImmutable;
use Exception;
use Iterator;
use Traversable;

class FooClass
{

	/** @var DateTimeImmutable */
	const DATE = true;

	/**
	 * @var DateTimeImmutable[]
	 */
	private $array = [];

	/**
	 * @param Traversable $foo
	 */
	public function __construct(Traversable $foo)
	{
		/** @var FooClass $boo */
		$boo = $this->get();
	}

	/**
	 * @return FooClass
	 * @throws Exception
	 */
	public function create(): self
	{
		return new self();
	}

	/**
	 * @param string|int|DateTimeImmutable $date
	 * @return DateTimeImmutable|null
	 */
	public function createDate($date)
	{

	}

	/**
	 * @return array|mixed[]|Traversable
	 */
	public function traversable()
	{

	}

	/**
	 * @return FooClass[]|Iterator
	 */
	public function resultSet()
	{
		/** @var $invalid Annotation */
		$coo = $this->get();
	}

}
