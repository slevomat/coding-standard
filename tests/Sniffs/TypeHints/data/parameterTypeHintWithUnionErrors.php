<?php // lint >= 8.0

class Whatever
{

	/** @param string|int $a */
	private function simple($a)
	{}

	/** @param string|true $a */
	private function withTrue($a)
	{}

	/** @param string|false $a */
	private function withFalse($a)
	{}

	/** @param string|null $a */
	private function withNull($a)
	{}

	/** @param int|string|null $a */
	private function moreWithNull($a)
	{}

	/** @param int[]|Anything $a */
	private function arrayIterable($a)
	{}

	/** @param int[]|Traversable $a */
	private function traversable($a)
	{}

	/** @param int[]|Traversable|ArrayIterator $a */
	private function moreTraversable($a)
	{}

	/** @param string|mixed[] $a */
	private function unionWithMixedArray($a = null)
	{}

	/** @param scalar $a */
	private function scalar($a)
	{}

	/** @param scalar|null $a */
	private function scalarNullable($a)
	{}

	/** @param numeric $a */
	private function numeric($a)
	{}

	/** @param numeric|null $a */
	private function numericNullable($a)
	{}

	/** @param scalar|numeric|null $a */
	private function scalarAndnumericNullable($a)
	{}

}
