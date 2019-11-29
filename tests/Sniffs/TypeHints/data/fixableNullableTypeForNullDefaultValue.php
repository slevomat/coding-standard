<?php // lint >= 7.4

class FooBar
{

	public function foo(\DateTimeImmutable & $dateTime = null)
	{
		function (string $string = null) {

		};

		fn (int $int = null) => $int;
	}

	public function invalid(bool $bool = null)
	{

	}

	public function withNullableParamBefore(?float $float, bool $param1 = null)
	{

	}

	public function withParamAfter(bool $param2 = null, ?array & ... $ellipsis)
	{

	}

	public function reference(float & $ref = null)
	{

	}

	public function multiLine(
		bool $param1 = null,
		array $param2 = null,
		callable $param3 = null,
		self $param4 = null
	)
	{

	}

}
