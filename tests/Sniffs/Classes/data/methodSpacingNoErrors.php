<?php

use Consistence\PhpException;
use React\Promise\ExtendedPromiseInterface;
use Slevomat\RabbitMq\FooTestMessage;
use Slevomat\RabbitMq\StopConsumerException;
use function React\Promise\reject;

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


	/****** completely useless comment ******/


	/**
	 * Comment
	 */
	public function forthMethod()
	{

	}


	/****** completely useless comment ******/


	public function fifthMethod()
	{

	}

	public function methodWithAnonymousClasses(): array
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

		};

		return [$anonymousClassA, $anonymousClassB];
	}

}
