<?php

$anonymousClass = new class('foo') {

	private $foo;

	public function __construct(string $foo)
	{
		$this->foo = $foo;
	}

	public function voidMethod(): void
	{
	}

	public function boolMethod(): bool
	{
		return true;
	}

	public function intMethod(): int
	{
		return 1;
	}

	public function floatMethod(): float
	{
		return 1.0;
	}

	public function stringMethod(): string
	{
		return $this->foo;
	}

	public function arrayMethod(...$values): array
	{
		return $values;
	}

	protected function anonymousClassMethod()
	{
		return new class {

			public function getFoo(): string
			{
				return 'foo';
			}
		};
	}

	private function nonClassMethodFunctionMethod()
	{
		function nonClassMethodFunction()
		{
		}
		return nonClassMethodFunction();
	}

	function untypedMethod($x)
	{
		return $x;
	}

	public function oneTooManyMethod(): string
	{
		return 'This method exceeds the maximum number of methods of this class.';
	}

};
