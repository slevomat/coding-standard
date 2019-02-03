<?php

/**
 * @see Anything
 * @property Invalid annotation
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

	/** @var callable(array<bool> $bools): (array<int>) */
	public $d;

}
