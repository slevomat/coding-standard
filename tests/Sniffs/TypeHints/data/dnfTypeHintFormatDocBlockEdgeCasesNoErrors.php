<?php

class Whatever
{

	/***/
	private $emptyDocComment;

	/**
	 * @param int| $invalid
	 */
	public function invalidAnnotation($invalid)
	{
	}

	/**
	 * @var int|string
	 */
	private $unionWithoutNull;

	/**
	 * @throws \RuntimeException
	 */
	public function throwsAnnotation()
	{
	}

}
