<?php // lint >= 8.1

class Whatever
{

	private Foo&Bar $two;

	private Foo&Bar&Boo $three;

	/** @var Foo|Bar */
	public $union;

}
