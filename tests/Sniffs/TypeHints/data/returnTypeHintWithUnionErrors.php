<?php // lint >= 8.0

class Whatever
{

	/** @return string|int */
	private function simple()
	{}

	/** @return string|true */
	private function withTrue()
	{}

	/** @return string|false */
	private function withFalse()
	{}

	/** @return string|null */
	private function withNull()
	{}

	/** @return int|string|null */
	private function moreWithNull()
	{}

	/** @return int[]|Anything */
	private function arrayIterable()
	{}

	/** @return int[]|Traversable */
	private function traversable()
	{}

	/** @return int[]|Traversable|ArrayIterator */
	private function moreTraversable()
	{}

	/** @return string|null */
	private function unionWithShortNullable(): ?string
	{}

	/** @return scalar */
	private function scalar()
	{}

	/** @return scalar|null */
	private function scalarNullable()
	{}

	/** @return numeric */
	private function numeric()
	{}

	/** @return numeric|null */
	private function numericNullable()
	{}

	/** @return scalar|numeric|null */
	private function scalarAndnumericNullable()
	{}

	/** @return object|void */
	private function objectAndVoid()
	{}

}
