<?php // lint >= 8.0

class Whatever
{

	/** @var string|int */
	private $simple;

	/** @var string|true */
	private $withTrue;

	/** @var string|false */
	private $withFalse;

	/** @var string|null */
	private $withNull;

	/** @var int|string|null */
	private $moreWithNull;

	/** @var int[]|Anything */
	private $arrayIterable;

	/** @var int[]|Traversable */
	private $traversable;

	/** @var int[]|Traversable|ArrayIterator */
	private $moreTraversable;

}
