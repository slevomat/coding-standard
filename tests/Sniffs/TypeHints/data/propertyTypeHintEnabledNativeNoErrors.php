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

}
