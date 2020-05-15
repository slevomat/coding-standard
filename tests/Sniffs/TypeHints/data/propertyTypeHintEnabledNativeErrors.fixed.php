<?php // lint >= 7.4

class Whatever
{

	private $noTypeHintNoAnnotation;

	/**
	 * @var int[]
	 */
	public array $arrayTypeHint;

	/**
	 * @var int[]|null
	 */
	public ?array $arrayTypeHintWithNull = null;

	/**
	 * @var array{foo: int}
	 */
	public array $arrayShapeTypeHint;

	public ?string $twoTypeWithNull = null;

	/**
	 * @var int[]|\Traversable
	 */
	public \Traversable $specificTraversable;

	public string $uselessDocComment;

	public array $missingAnnotationForTraversable;

	/**
	 * @var array
	 */
	public array $missingItemsSpecification;

	/**
	 * @var \Closure(): array
	 */
	public \Closure $callable;

	/**
	 * @var \Traversable
	 */
	public \Traversable $onlyTraversable;

	/**
	 * @var array{array}
	 */
	public array $arrayShapeWithoutItemsSpecification;

	/**
	 * @var \Generic<array>
	 */
	public \Generic $genericWithoutItemsSpecification;

	/**
	 * @var array[]&\Traversable
	 */
	public \Traversable $traversableIntersection;

	/**
	 * @var \Traversable&array[]
	 */
	public \Traversable $traversableIntersectionDifferentOrder;

	/**
	 * @var null|\Traversable
	 */
	public ?\Traversable $traversableNull = null;

	public object $object;

	/**
	 * Description.
	 *
	 */
	public string $uselessAnnotationWithDescription;

	/**
	 * @see Anything
	 */
	public string $uselessAnnotationWithOtherAnnotation;

	static public int $staticFirst;

	public static int $staticSecond;

	/** @var array<string>|array<int> */
	public array $unionWithSameBase;

	/** @var array<string>|array<int>|array<bool> */
	public array $unionWithSameBaseAndMoreTypes;

	/** @var array<int>|bool[] */
	public array $unionWithSameBaseToo;

	/** @var array<string>|array<int>|array<bool>|null */
	public ?array $unionWithSameNullableBase = null;

	/** @var ?int */
	public ?int $nullable = null;

	/**
	 * @var mixed[]|array
	 */
	public array $traversableArray;

	/** @var true */
	public bool $constTrue;

	/** @var FALSE */
	public bool $constFalse;

	/** @var 0 */
	public int $constInteger;

	/** @var 0.0 */
	public float $constFloat;

	/** @var 'foo' */
	public string $constString;

	/** @var 'foo'|null */
	public ?string $constNullableString = null;

	/** @var 'foo'|'bar' */
	public string $constUnionString;

	/** @var class-string */
	public string $classString;

}
