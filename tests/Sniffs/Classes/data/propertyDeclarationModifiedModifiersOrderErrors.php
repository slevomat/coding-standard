<?php // lint >= 8.1

class Whatever
{

	private static $withoutTypeHint = 'false';

	private readonly ?string $nullable;

	public static int $notNullable = 0;

	public readonly int|float $union;

}
