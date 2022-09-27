<?php // lint >= 8.1

class Whatever
{

	/**
	 */
	private function two(): Foo&Bar
	{
	}

	/***/
	public function three(): Foo&Bar&Boo
	{
	}

	/** @return Foo|Bar */
	public function union()
	{
	}

}
