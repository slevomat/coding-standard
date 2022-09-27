<?php // lint >= 8.1

class Whatever
{

	/**
	 */
	private function two(Foo&Bar $a)
	{
	}

	/***/
	public function three(Foo&Bar&Boo $a)
	{
	}

	/** @param Foo|Bar $a */
	public function union($a)
	{
	}

}
