<?php

namespace FooNamespace;

function returnsVoid()
{
	return;
}

function returnsNothing()
{
}

/**
 * @return void
 */
function voidAnnotation()
{

}


abstract class Foo
{

	public function __construct()
	{

	}

	public function __destruct()
	{

	}

	public function __clone()
	{

	}

	public function returnsVoid()
	{
		return;
	}

	protected function returnsNothing()
	{
	}

	/**
	 * @return void
	 */
	public abstract function abstractVoidAnnotation();

	/**
	 * @return void
	 */
	public function voidAnnotation()
	{

	}

}
