<?php // lint >= 8.4

class Whatever
{

	private $noTypeHintNoAnnotation;

	/**
	 * @var int[]
	 */
	public $arrayTypeHint;

	/**
	 * @var int[]|null
	 */
	public $arrayTypeHintWithNull;

	/**
	 * @var array{foo: int}
	 */
	public $arrayShapeTypeHint;

	/**
	 * @var string|null
	 */
	public $twoTypeWithNull;

	/**
	 * @var int[]|\Traversable
	 */
	public $specificTraversable;

	/**
	 * @var string
	 */
	public string $uselessDocComment;

	public array $missingAnnotationForTraversable;

	/**
	 * @var array
	 */
	public array $missingItemsSpecification;

	/**
	 * @var \Closure(): array
	 */
	public $callable;

	/**
	 * @var \Traversable
	 */
	public $onlyTraversable;

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
	public $traversableIntersection;

	/**
	 * @var \Traversable&array[]
	 */
	public $traversableIntersectionDifferentOrder;

	/**
	 * @var null|\Traversable
	 */
	public $traversableNull;

	/**
	 * @var object
	 */
	public $object;

	/**
	 * Description.
	 *
	 * @var string
	 */
	public string $uselessAnnotationWithDescription;

	/**
	 * @see Anything
	 * @var string
	 */
	public string $uselessAnnotationWithOtherAnnotation;

	/** @var int */
	static public $staticFirst;

	/** @var int */
	public static $staticSecond;

	/** @var array<string>|array<int> */
	public $unionWithSameBase;

	/** @var array<string>|array<int>|array<bool> */
	public $unionWithSameBaseAndMoreTypes;

	/** @var array<int>|bool[] */
	public $unionWithSameBaseToo;

	/** @var array<string>|array<int>|array<bool>|null */
	public $unionWithSameNullableBase;

	/** @var ?int */
	public $nullable;

	/**
	 * @var mixed[]|array
	 */
	public $traversableArray;

	/** @var true */
	public $constTrue;

	/** @var FALSE */
	public $constFalse;

	/** @var 0 */
	public $constInteger;

	/** @var 0.0 */
	public $constFloat;

	/** @var 'foo' */
	public $constString;

	/** @var 'foo'|null */
	public $constNullableString;

	/** @var 'foo'|'bar' */
	public $constUnionString;

	/** @var class-string */
	public $classString;

	/** @var mixed */
	public $mixed;

	/** @var mixed|null */
	public $nullableMixed;

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.UselessAnnotation
	 * @var string[]
	 */
	private array $uselessSuppressOfUselessAnnotation;

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingTraversableTypeHintSpecification
	 * @var string[]
	 */
	private array $uselessSuppressOfMissingTraversableTypeHintSpecification;

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingAnyTypeHint
	 * @phpstan-var class-string
	 */
	private $uselessSuppressOfMissingAnyTypeHint;

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingAnyTypeHint
	 * @phpstan-var class-string
	 */
	private string $uselessSuppressOfMissingAnyTypeHintWithTypeHint;

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
	 * @phpstan-var class-string
	 */
	private string $uselessSuppressOfMissingNativeTypeHintWithTypeHint;

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
	 * @var callable
	 */
	private $uselessSuppressOfMissingNativeTypeHint;

	/** @var ?int */
	private ?int $uselessAnnotationWithShortNullable;

	/** @var int */
	static $staticProperty;

	/** @var int */
	final $finalProperty;

}
