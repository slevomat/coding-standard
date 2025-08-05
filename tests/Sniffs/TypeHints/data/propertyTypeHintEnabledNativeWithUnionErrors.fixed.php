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

	private string|int|float|bool $scalar;

	private string|int|float|bool|null $scalarNullable = null;

	private int|float|string $numeric;

	private int|float|string|null $numericNullable = null;

	private string|int|float|bool|null $scalarAndnumericNullable = null;

}
