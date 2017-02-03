<?php // lint >= 7.1

namespace FooNamespace;

use ArrayIterator;
use DateTimeImmutable;

/**
 * @return int
 */
function simpleTypeHint(): int
{
	return 0;
}

/**
 * @return \Foo
 */
function fullyQualifiedClassTypeHint(): \Foo
{
	return new Foo();
}

/**
 * @return DateTimeImmutable
 */
function usedClassTypeHint(): DateTimeImmutable
{
	return new DateTimeImmutable();
}

/**
 * @return resource
 */
function unofficialTypeHint()
{
	return null;
}

/**
 * @return int|null
 */
function nullableSimpleTypeHint(): ?int
{
	return 0;
}

/**
 * @return null|int
 */
function nullableSimpleTypeHintFirst(): ?int
{
	return 0;
}

/**
 * @return \Foo|null
 */
function nullableFullyQualifiedClassTypeHint(): ?\Foo
{
	return new Foo();
}

/**
 * @return DateTimeImmutable|null
 */
function nullableUsedClassTypeHint(): ?DateTimeImmutable
{
	return new DateTimeImmutable();
}

/**
 * @return resource|null
 */
function nullableUnofficialTypeHint()
{
	return null;
}

/**
 * @return mixed[]|array
 */
function arrayTypeHint(): array
{
	return [];
}

/**
 * @return array|mixed[]
 */
function arrayTypeHintFirst(): array
{
	return [];
}

/**
 * @return mixed[]|iterable
 */
function iterableTypeHint(): iterable
{
	return [];
}

/**
 * @return mixed[]|\Traversable
 */
function travesableFullyQualifiedTypeHint(): \Traversable
{
	return new ArrayIterator();
}

/**
 * @return mixed[]|ArrayIterator
 */
function travesableUsedTypeHint(): ArrayIterator
{
	return new ArrayIterator();
}

/**
 * @return string[]|null
 */
function returnsNullableArrayOfStrings()
{
	return [];
}

/**
 * @return mixed[]
 */
function returnsTraversableMixed()
{
	return [];
}

/**
 * @return string[]
 */
function returnsTraversableStrings()
{
	return [];
}

/**
 * @return \DateTimeImmutable[]
 */
function returnsTraversableDateTimeImmutable()
{
	return [];
}

abstract class Foo
{

	/**
	 * @return int
	 */
	public function simpleTypeHint(): int
	{
		return 0;
	}

	/**
	 * @return \Foo
	 */
	public function fullyQualifiedClassTypeHint(): \Foo
	{
		return new Foo();
	}

	/**
	 * @return DateTimeImmutable
	 */
	public function usedClassTypeHint(): DateTimeImmutable
	{
		return new DateTimeImmutable();
	}

	/**
	 * @return resource
	 */
	public function unofficialTypeHint()
	{
		return null;
	}

	/**
	 * @return int|null
	 */
	private function nullableSimpleTypeHint(): ?int
	{
		return 0;
	}

	/**
	 * @return null|int
	 */
	private function nullableSimpleTypeHintFirst(): ?int
	{
		return 0;
	}

	/**
	 * @return \Foo|null
	 */
	private function nullableFullyQualifiedClassTypeHint(): ?\Foo
	{
		return new Foo();
	}

	/**
	 * @return DateTimeImmutable|null
	 */
	private function nullableUsedClassTypeHint(): ?DateTimeImmutable
	{
		return new DateTimeImmutable();
	}

	/**
	 * @return resource|null
	 */
	private function nullableUnofficialTypeHint()
	{
		return null;
	}

	/**
	 * @return mixed[]|array
	 */
	protected function arrayTypeHint(): array
	{
		return [];
	}

	/**
	 * @return array|mixed[]
	 */
	protected function arrayTypeHintFirst(): array
	{
		return [];
	}

	/**
	 * @return mixed[]|iterable
	 */
	protected function iterableTypeHint(): iterable
	{
		return [];
	}

	/**
	 * @return mixed[]|\Traversable
	 */
	protected function travesableFullyQualifiedTypeHint(): \Traversable
	{
		return new ArrayIterator();
	}

	/**
	 * @return mixed[]|ArrayIterator
	 */
	protected function travesableUsedTypeHint(): ArrayIterator
	{
		return new ArrayIterator();
	}

	/**
	 * @return string
	 */
	abstract function abstractMethod($a): string;

	/**
	 * @return string
	 */
	public static function staticMethod($a): string
	{
		return '';
	}

	/**
	 * @return string[]|null
	 */
	public function returnsNullableArrayOfStrings()
	{
		return [];
	}

	/**
	 * @return mixed[]
	 */
	private function returnsTraversableMixed()
	{
		return [];
	}

	/**
	 * @return string[]
	 */
	public static function returnsTraversableStrings()
	{
		return [];
	}

	/**
	 * @return \DateTimeImmutable[]
	 */
	abstract function returnsTraversableDateTimeImmutable();

}
