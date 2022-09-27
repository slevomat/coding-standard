<?php // lint >= 8.1

class Whatever
{

	/**
	 * @return Foo&Bar
	 */
	private function two()
	{
	}

	/** @return Foo&Bar&Boo */
	public function three()
	{
	}

	/** @return Foo|Bar */
	public function union()
	{
	}

}
