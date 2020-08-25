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

	/** @return iterable<array<string>> */
	public function l(): iterable
	{
	}

	/** @param iterable<array<string>> $n */
	public function m(iterable $n)
	{
	}

	/** @return \ArrayObject<array<string>> */
	public function o(): ArrayObject
	{
	}

	/** @param \ArrayObject<array<string>> $p */
	public function p(ArrayObject $p)
	{
	}

	/** @param array<array<string>> $q */
	public function q($q)
	{
	}

	/** @param array{int, array<int>} $r */
	public function r($r)
	{
	}

	private function s(): void
	{
		/**
		 * @param array<int> $data
		 * @return array<mixed>
		 */
		$closure = function (array $data): array {};
	}

}
