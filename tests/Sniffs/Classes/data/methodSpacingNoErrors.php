<?php

function a()
{

}

class WithOneMethod
{

	public function method()
	{

	}

}

class WithAnonymousClass
{
	public function method()
	{
		return new class {
			public function method()
			{

			}
		};
	}

	public function secondMethod()
	{
	}
}

abstract class Whatever
{

	public function method()
	{

	}

	abstract public function secondMethod();

	/**
	 * With Comment
	 */
	public function thirdMethod()
	{

	}

}
