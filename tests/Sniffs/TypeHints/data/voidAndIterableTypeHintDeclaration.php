<?php // lint >= 7.1

abstract class VoidAndIterableClass
{

	/**
	 * @param iterable $list
	 * @param mixed $bar
	 */
	public function doFoo($list, $bar)
	{
		return;
	}

	/**
	 * @param string[] $list
	 * @return void
	 */
	public function doBar(iterable $list): void
	{
		return;
	}

	/**
	 * @return string[]
	 */
	public function returnsIterable(): iterable
	{
		return [];
	}

	/**
	 * @return void
	 */
	public function returnVoid(): void
	{
		return;
	}

	abstract public function abstractReturnVoid(): void;

}
