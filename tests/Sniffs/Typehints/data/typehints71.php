<?php // lint >= 7.1

class Typehints71FooClass
{

	/**
	 * @param iterable $list
	 * @param mixed $bar
	 * @return void
	 */
	public function doFoo($list, $bar)
	{
		return;
	}

	/**
	 * @param iterable $list
	 * @return void
	 */
	public function doBar(iterable $list): void
	{
		return;
	}

}
