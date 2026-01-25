<?php // lint >= 8.5

class ParentClass
{

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint
	 * @var string[]
	 */
	public $arrayTypeHint;

}

class Whatever extends ParentClass
{

	use Anything {
		doSomething as public;
	}

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
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.UselessAnnotation
	 * @var array
	 */
	private array $isSniffCodeMissingTravesableTypeHintSpecificationSuppressed;

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.UselessAnnotation
	 * @var int
	 */
	private int $isSniffCodeUselessAnnotationSuppressed;

	#[Override]
	protected $withOverride;

	/**
	 * @var int
	 */
	#[Override]
	protected $withAnnotationAndOverride;

	#[Override]
	protected array $withoutTraversableTypeHintSpecificationButWithOverride;

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
	 * @var scalar
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

	/** @var \Foo::INTEGER $a */
	public $constTypeNode;

	/**
	 * @var WeakMap<static, object{a: int}> https://phpstan.org/writing-php-code/phpdoc-types#object-shapes
	 */
	public WeakMap $objectShapeInItems;

	#[Override]
	public $arrayTypeHint = ['hello'];

	public function __construct(private $propertyPromotion)
	{
	}

}

/**
 * @phpstan-type SomeAlias1 int|false
 * @phpstan-import-type SomeAlias2 from \SomeClass
 * @phpstan-import-type SomeAlias4 from \SomeClass as SomeAlias3
 */
class Aliases
{

	/**
	 * @var SomeAlias1
	 */
	public $withAlias1;

	/** @var SomeAlias2 */
	protected $withAlias2;

	/**
	 * @var SomeAlias3
	 */
	private static $withAlias3;

	/**
	 * @var SomeAlias3
	 */
	private array $withArrayAlias4;

	/**
	 * @var SomeAlias2|null
	 */
	private ?array $withArrayAlias5;
}

interface SomeInterface
{
	public static function someMethod(): static;
}
