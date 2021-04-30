<?php

class methodPerClassLimitNoErrors
{

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

	private function selfMethod(): self
	{
		return $this;
	}

	function untypedMethod($x)
	{
		return $x;
	}

	public function oneTooManyMethod(): string
	{
		return 'This method exceeds the maximum number of methods of this class.';
	}

}
