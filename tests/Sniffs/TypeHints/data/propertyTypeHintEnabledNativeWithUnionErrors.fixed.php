<?php // lint >= 8.0

class Whatever
{

	private string|int $simple;

	/** @var string|true */
	private string|bool $withTrue;

	private string|false $withFalse;

	private ?string $withNull = null;

	private int|string|null $moreWithNull = null;

	/** @var int[]|Anything */
	private array|Anything $arrayIterable;

	/** @var int[]|Traversable */
	private Traversable $traversable;

	/** @var int[]|Traversable|ArrayIterator */
	private Traversable|ArrayIterator $moreTraversable;

}
