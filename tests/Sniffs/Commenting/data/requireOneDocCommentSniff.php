<?php

use Slevomat\Foo;
use Slevomat\FooTask;

/**
 * Description
 */
/**
 * Description 2
 */
class Whatever
{

	/**
	 * Description
	 * @var string
	 */
	/**
	 * @var int
	 */
	private $property;

	/** @phpstan-assert-if-true !null $this->getApprovedTime() */
	/** @phpstan-assert-if-true null $this->getRejectedTime() */
	public function method()
	{
		/** @var int $productId */
		/** @var Foo $issue */
		return [1, 2, 3];
	}

	/**
	 * @param int $a
	 * @return void
	 */
	public function bar(int $a)
	{
		/** @var int */
		return 5;
	}

	/**
	 * @param int $a
	 * @return void
	 */
	public function bar2(int $a)
	{
		/** @var int */
		/** @var int */
		return 5;
	}

	/**
	 * @param FooTask $backgroundExportTask
	 * @return bool
	 */
	// public function isAllowed(FooTask $task): bool;

	/**
	 * @param FooTask $backgroundExportTask
	 */
	// public function execute(FooTask $task): BackgroundExportFile;
}
