<?php // lint >= 8.0

class Whatever
{

	/***/
	private function simple(): string|int
	{}

	/** @return string|true */
	private function withTrue(): string|bool
	{}

	/***/
	private function withFalse(): string|false
	{}

	/***/
	private function withNull(): ?string
	{}

	/***/
	private function moreWithNull(): int|string|null
	{}

	/** @return int[]|Anything */
	private function arrayIterable(): array|Anything
	{}

	/** @return int[]|Traversable */
	private function traversable(): Traversable
	{}

	/** @return int[]|Traversable|ArrayIterator */
	private function moreTraversable(): Traversable|ArrayIterator
	{}

}
