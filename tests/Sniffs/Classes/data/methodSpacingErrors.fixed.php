<?php

abstract class Whatever
{

	public function method()
	{

	}

	abstract public function secondMethod();

	/**
	 * Whatever
	 */
	public function thirdMethod()
	{

	}

	public function forthMethod()
	{

	}

	public function fifthMethod(): array
	{
		$anonymousClassA = new class () extends Exception {

			public function returnTrue(): bool
			{
				return true;
			}
		};
		$anonymousClassB = new class () extends Exception {

			public function returnTrue(): bool
			{
				return true;
			}

			public function returnFalse(): bool
			{
				return false;
			}

		};

		return [$anonymousClassA, $anonymousClassB];
	}

	public function sixthMethod()
	{

	}

}
