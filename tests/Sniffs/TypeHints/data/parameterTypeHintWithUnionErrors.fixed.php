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

}
