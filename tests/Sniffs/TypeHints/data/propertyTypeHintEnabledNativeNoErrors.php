<?php // lint >= 7.4

class Whatever
{

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint
	 */
	private $isSniffSuppressed;

	/**
	 * {@inheritdoc}
	 */
	private $hasInheritdocAnnotation;

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingAnyTypeHint
	 */
	private $isSniffCodeAnyTypeHintSuppressed;

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
	 * @var int
	 */
	private $isSniffCodeMissingNativeTypeHintSuppressed;

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingTraversableTypeHintSpecification
	 * @var array
	 */
	private array $isSniffCodeMissingTravesableTypeHintSpecificationSuppressed;

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.UselessAnnotation
	 * @var int
	 */
	private int $isSniffCodeUselessAnnotationSuppressed;

	private int $noTraversableType;

	/**
	 * @var int[]
	 */
	private array $withTraversableTypeHintSpecification;

	/**
	 * @var null
	 */
	public $null;

	/**
	 * @var string|int|bool
	 */
	public $aLotOfTypes;

	/**
	 * @var string|int
	 */
	public $twoTypeNoNullOrTraversable;

	/**
	 * @var mixed
	 */
	public $invalidType;

	/**
	 * @var int[]|\DateTimeImmutable
	 */
	public $twoTypesNoTraversable;

	/**
	 * @var \Boo<bool>|\Foo
	 */
	public $generic;

	/**
	 * @var
	 */
	public int $emptyAnnotation;

	/**
	 * @var
	 */
	public int $invalidAnnotation;

	/**
	 * @var $this
	 */
	public self $containsThis;

	/**
	 * @var $this|null
	 */
	public ?self $containsThisOrNull;

	/**
	 * @var array<string, callable(mixed $value) : string>
	 */
	public array $callableArray;

	/**
	 * @var callable
	 */
	public $callable;

	/**
	 * @var Whatever|Something|Anything
	 */
	public $unionWithDifferentBase;

	/**
	 * @var array<int>|array<bool>|(A&B)
	 */
	public $unionWithMoreDifferentBase;

	/**
	 * @var Whatever|Something|Anything|null
	 */
	public $unionWithDifferentNullableBase;

	/** @var mixed[]|array|Traversable */
	public $moreTraverasableTypes;

	/**
	 * @var mixed[]|array|SomethingThatLooksAsArray
	 */
	public $moreDifferentTypes;

	/**
	 * @var int[]|string[]|Anything
	 */
	public $anotherDifferentTypes;

	/**
	 * @var Whatever|mixed[]|array
	 */
	public $yetAnotherDifferentTypes;

	/**
	 * @psalm-var Whatever<int>
	 */
	public $withPsalmAnnotationAndMissingNativeTypeHint;

	/**
	 * @phpstan-var Whatever<int>
	 */
	public $withPhpstanAnnotationAndMissingNativeTypeHint;

	/**
	 * @psalm-var
	 * @psalm-var Whatever<int>
	 */
	public array $withPsalmAnnotationAndTraversableNativeTypeHint;

	/**
	 * @phpstan-var
	 * @phpstan-var Whatever<int>
	 */
	public array $withPhpstanAnnotationAndTraversableNativeTypeHint;

	/** @var array<int, ?Whatever> */
	public array $traversableWithNullableItem;

	/** @var class-string */
	public string $classString;

}
