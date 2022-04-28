<?php // lint = 8.1

class Whatever
{

	private int $noSpace;

	private int $moreSpaces;

	private ?int $spacesAfterNullabilitySymbol;

	private ?int $noSpaceBeforeNullabilitySymbol;

	private ?int $moreSpaceBeforeNullabilitySymbol;

	private int $moreSpaceBeforeTypeHint;

	private int|float $union = 0;

	private null|float $nullableUnion = 0.0;

	private false|int $unionWithFalse = false;

	readonly int $readonly = 0;

	static string $static = 'string';

	public static string $static = 'string';

}
