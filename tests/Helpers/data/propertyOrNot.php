<?php // lint >= 8.4

class Foo
{

	/**
	 * @var bool
	 */
	public $boolean = true;

	private static $string = 'string';

	public function __construct(private $propertyPromotion, $boo, public readonly Foo|Bar $propertyPromotionWithTypeHint)
	{
		$hoo = $boo;
	}

	private$weirdDefinition;

	private ?\Whatever\Anything $withTypeHint;

	private int $withSimpleTypeHint;

	public function whatever()
	{

	}

	private string $typedPropertyAfterMethod;

	private string|int $propertyWithUnionTypeHint;

	private null|int $nullableWithNullOnStart;
	private string|null|int $nullableWithNullInTheMiddle;
	private string|null $nullableWithNullAtTheEnds;

	private string | int | false | null $unionWithSpaces;

	private readonly int $withReadonlyLast;

	readonly public int $withReadonlyFirst;

	readonly int $onlyReadonlyProperty;

	readonly string|int $onlyReadonlyPropertyWithTypeHint;

	public private(set) readonly ?string $privateSet;

	protected(set) array $protectedSet;

	protected(set) public int $protectedSetPublic;

	private(set) protected ?string $privateSetProtected;

	final ?bool $final;

	public final ?bool $publicFinal;

	static protected array $staticProtected = [];

	public string $propertyWithHooks {
		get => 'mailto:' . $this->propertyWithHooks;
		set (string $propertyWithHooksValue) {
			$this->propertyWithHooks = $propertyWithHooksValue;
		}
	}

	static $onlyStatic;

}
