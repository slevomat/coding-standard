<?php

use Doctrine;
use Doctrine\ORM\Mapping as ORM;

/**
 * @return void
 */
function fooFunctionWithReturnAnnotation()
{

}

function fooFunctionWithReturnTypeHint(): FooClass
{
	return new FooClass();
}

/**
 * @param Doctrine\Common\Collections\ArrayCollection $parameter
 */
function fooFunctionWithParameterAnnotation($parameter)
{

}

function fooFunctionWithParameterTypeHint(Doctrine\Common\Collections\ArrayCollection $parameter)
{

}

class FooClass
{

	/**
	 * @return FooClass
	 */
	public function fooMethodWithReturnAnnotation()
	{
		return new self();
	}


	public function fooMethodWithReturnTypeHint(): bool
	{
		return true;
	}

	/**
	 * @param ORM\Id $parameter
	 */
	public function fooMethodWithParameterAnnotation($parameter)
	{

	}

	public function fooMethodWithParameterTypeHint(ORM\Id $parameter)
	{

	}

}
