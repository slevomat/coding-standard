<?php

/**
 * @param integer $a
 * @return boolean
 */
function doSomething($a)
{
	return true;
}

class Foo
{

	/** @var integer|null */
	private $integer = 0;

	/** @var boolean */
	private $boolean = true;

	/**
	 * @param boolean|null $a
	 * @return integer|null
	 */
	public function doSomething($a)
	{
		return 0;
	}

}
