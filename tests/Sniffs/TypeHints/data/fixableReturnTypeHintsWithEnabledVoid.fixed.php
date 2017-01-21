<?php // lint >= 7.1

namespace FooNamespace;

function returnsVoid(): void
{
	return;
}

function returnsNothing(): void
{
}

/**
 * @return void
 */
function voidAnnotation(): void
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

	public function returnsVoid(): void
	{
		return;
	}

	protected function returnsNothing(): void
	{
	}

	/**
	 * @return void
	 */
	public abstract function voidAnnotation(): void;

}
