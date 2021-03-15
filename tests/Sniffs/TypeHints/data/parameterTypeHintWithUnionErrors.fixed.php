<?php // lint >= 8.0

class Whatever
{

	/***/
	private function simple(string|int $a)
	{}

	/** @param string|true $a */
	private function withTrue(string|bool $a)
	{}

	/***/
	private function withFalse(string|false $a)
	{}

	/***/
	private function withNull(?string $a)
	{}

	/***/
	private function moreWithNull(int|string|null $a)
	{}

	/** @param int[]|Anything $a */
	private function arrayIterable(array|Anything $a)
	{}

	/** @param int[]|Traversable $a */
	private function traversable(Traversable $a)
	{}

	/** @param int[]|Traversable|ArrayIterator $a */
	private function moreTraversable(Traversable|ArrayIterator $a)
	{}

	/** @param string|mixed[] $a */
	private function unionWithMixedArray(string|array $a = null)
	{}

	/***/
	private function scalar(string|int|float|bool $a)
	{}

	/***/
	private function scalarNullable(string|int|float|bool|null $a)
	{}

	/***/
	private function numeric(int|float $a)
	{}

	/***/
	private function numericNullable(int|float|null $a)
	{}

	/***/
	private function scalarAndnumericNullable(string|int|float|bool|null $a)
	{}

}
