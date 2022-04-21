<?php // lint >= 8.1

class Foo
{

	/**
	 * @var bool
	 */
	public $boolean = true;

	private static $string = 'string';

	public function __construct(private $propertyPromotion, $boo)
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
}
