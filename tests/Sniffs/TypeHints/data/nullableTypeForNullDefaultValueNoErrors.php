<?php // lint >= 7.4

function foo(?\DateTimeImmutable & $dateTime = null)
{

}

$callback = function (?string $string = null) {

};

fn (?int $int = null) => $int;

trait Foo
{

	public function doFoo(?int $int = null)
	{

	}

}

class FooBar extends \stdClass
{

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.NullableTypeForNullDefaultValue
	 */
	private function isSniffSuppressed(string $a = null)
	{

	}

	public function valid(?bool $bool = null, $noTypehint = null, ?int $int, string $a = 'default', ?string $b = 'null')
	{

	}

	public function myself(?self $self = null)
	{

	}

	public function array(?array $array = null)
	{

	}

	public function parent(?parent $parent = null)
	{

	}

	public function callable(?callable $callable = null)
	{

	}

	public function withNullableParamBefore(float $float, ?\Foo\Bar\Baz $param1 = null)
	{

	}

	public function withParamAfter(?bool $param2 = null, array ...$ellipsis)
	{

	}

	public function reference(?float & $ref = null, ...$test)
	{

	}

}

array_map(
	static fn (array $value): array => array_filter($value),
	[]
);
