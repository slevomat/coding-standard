<?php // lint >= 8.1

class Whatever
{

	/**
	 * @param Foo&Bar $a
	 */
	private function two($a)
	{
	}

	/** @param Foo&Bar&Boo $a */
	public function three($a)
	{
	}

	/** @param Foo|Bar $a */
	public function union($a)
	{
	}

}
