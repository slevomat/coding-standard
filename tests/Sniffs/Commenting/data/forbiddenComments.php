<?php

/**
 * Created by PhpStorm.
 */
class Foo extends Whatever
{

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();

	}

	/**
	 * Foo
	 * Bar
	 *
	 * @throws \Throwable Text text text text
	 * text text text text text
	 * @throws \TypeError Text text
	 * text text text
	 *
	 */
	public function multiLine()
	{

	}

	/**
	 * Description description description
	 *  description description
	 * blahblahblah
	 *
	 * @see https://www.slevomat.cz
	 */
	public function withDescription()
	{
	}

	public function inlineComments()
	{
		/** @var string $string blahblahblah */

		/** blahblahblah */
	}

}
