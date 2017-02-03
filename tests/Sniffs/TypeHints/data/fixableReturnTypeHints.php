<?php // lint >= 7.1

namespace FooNamespace;

use ArrayIterator;
use DateTimeImmutable;

/**
 * @return int
 */
function simpleTypeHint()
{
	return 0;
}

/**
 * @return \Foo
 */
function fullyQualifiedClassTypeHint()
{
	return new Foo();
}

/**
 * @return DateTimeImmutable
 */
function usedClassTypeHint()
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
function nullableSimpleTypeHint()
{
	return 0;
}

/**
 * @return null|int
 */
function nullableSimpleTypeHintFirst()
{
	return 0;
}

/**
 * @return \Foo|null
 */
function nullableFullyQualifiedClassTypeHint()
{
	return new Foo();
}

/**
 * @return DateTimeImmutable|null
 */
function nullableUsedClassTypeHint()
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
function arrayTypeHint()
{
	return [];
}

/**
 * @return array|mixed[]
 */
function arrayTypeHintFirst()
{
	return [];
}

/**
 * @return mixed[]|iterable
 */
function iterableTypeHint()
{
	return [];
}

/**
 * @return mixed[]|\Traversable
 */
function travesableFullyQualifiedTypeHint()
{
	return new ArrayIterator();
}

/**
 * @return mixed[]|ArrayIterator
 */
function travesableUsedTypeHint()
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
	public function simpleTypeHint()
	{
		return 0;
	}

	/**
	 * @return \Foo
	 */
	public function fullyQualifiedClassTypeHint()
	{
		return new Foo();
	}

	/**
	 * @return DateTimeImmutable
	 */
	public function usedClassTypeHint()
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
	private function nullableSimpleTypeHint()
	{
		return 0;
	}

	/**
	 * @return null|int
	 */
	private function nullableSimpleTypeHintFirst()
	{
		return 0;
	}

	/**
	 * @return \Foo|null
	 */
	private function nullableFullyQualifiedClassTypeHint()
	{
		return new Foo();
	}

	/**
	 * @return DateTimeImmutable|null
	 */
	private function nullableUsedClassTypeHint()
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
	protected function arrayTypeHint()
	{
		return [];
	}

	/**
	 * @return array|mixed[]
	 */
	protected function arrayTypeHintFirst()
	{
		return [];
	}

	/**
	 * @return mixed[]|iterable
	 */
	protected function iterableTypeHint()
	{
		return [];
	}

	/**
	 * @return mixed[]|\Traversable
	 */
	protected function travesableFullyQualifiedTypeHint()
	{
		return new ArrayIterator();
	}

	/**
	 * @return mixed[]|ArrayIterator
	 */
	protected function travesableUsedTypeHint()
	{
		return new ArrayIterator();
	}

	/**
	 * @return string
	 */
	abstract function abstractMethod($a);

	/**
	 * @return string
	 */
	public static function staticMethod($a)
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
