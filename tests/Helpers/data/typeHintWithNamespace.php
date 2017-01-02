<?php

namespace FooNamespace;

/**
 * @return void
 */
function fooFunctionWithAnnotation()
{

}

function fooFunctionWithReturnTypeHint(): FooClass
{
	return new FooClass();
}

class FooClass
{

	/**
	 * @return FooClass
	 */
	public function fooMethodWithAnnotation()
	{
		return new self();
	}


	public function fooMethodWithReturnTypeHint(): bool
	{
		return true;
	}

}
