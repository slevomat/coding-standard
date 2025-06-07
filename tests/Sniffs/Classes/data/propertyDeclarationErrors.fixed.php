<?php // lint = 8.4

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

	public static $publicStatic;

	private readonly int $privateReadonly;

}

abstract class Something
{

	final public int $publicFinal = 0;

	public private(set) readonly ?string $readonlyPublicPrivateSet;

	protected static array $staticProtected = [];

}
