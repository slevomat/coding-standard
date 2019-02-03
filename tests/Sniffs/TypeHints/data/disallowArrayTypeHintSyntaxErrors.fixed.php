<?php

use Traversable;

/**
 * @property array<\DateTimeImmutable> $property
 * @method array<bool> method(?ArrayObject<array<int>> $m)
 */
class Whatever
{

	/** @var array<(array<\DateTimeImmutable>|array<\DateTime>|null)> */
	public $a;

	/** @var \ArrayObject<array<int>> */
	public $b;

	/** @var \ArrayObject&array<int> */
	public $c;

	/** @var callable(array<bool> $bools): array<int> */
	public $d;

	/** @var array<array<array<int>>> */
	public $e;

	/** @var \ArrayObject<int> */
	public $f;

	/** @var Traversable<array<int>> */
	public $g;

	/** @var iterable<bool> */
	public $h;

	/** @var array<iterable<int>> */
	public $i;

	/** @var \Anything|array<string> */
	public $j;

	/** @var array<int>|string */
	public $k;

}
