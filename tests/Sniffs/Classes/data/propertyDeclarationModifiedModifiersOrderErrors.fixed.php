<?php // lint >= 8.1

class Whatever
{

	static private $withoutTypeHint = 'false';

	readonly private ?string $nullable;

	static public int $notNullable = 0;

	readonly public int|float $union;

}
