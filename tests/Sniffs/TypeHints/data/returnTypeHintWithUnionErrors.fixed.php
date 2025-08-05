<?php // lint >= 8.0

class Whatever
{

	/** */
	private function simple(): string|int
	{}

	/** @return string|true */
	private function withTrue(): string|bool
	{}

	/** */
	private function withFalse(): string|false
	{}

	/** */
	private function withNull(): ?string
	{}

	/** */
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

	/** */
	private function unionWithShortNullable(): ?string
	{}

	/** */
	private function scalar(): string|int|float|bool
	{}

	/** */
	private function scalarNullable(): string|int|float|bool|null
	{}

	/** */
	private function numeric(): int|float|string
	{}

	/** */
	private function numericNullable(): int|float|string|null
	{}

	/** */
	private function scalarAndnumericNullable(): string|int|float|bool|null
	{}

	/** */
	private function objectAndVoid(): object|null
	{}

	/** */
	private function mixedAndVoid(): mixed
	{}

	/** @return non-empty-array|null */
	public function returnNonEmptyArray(): ?array
	{}

}
