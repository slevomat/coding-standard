<?php // lint >= 8.1

class Whatever
{

	use Anything {
		doSomething as public;
	}

	public const WHATEVER = 0;

	public function whatever()
	{
	}

	private $withoutTypeHint = 'false';

	private ?string $nullable = 'string';

	private int $notNullable = 0;

	private int|float $union = 0;

	private null|float $nullableUnion = 0.0;

	private false|int $unionWithFalse = false;

	public readonly bool $readonly;

	public function __construct(private  int  $propertyPromotion)
	{
	}

}

interface SomeInterface
{
	public static function someMethod(): static;
}

class UserEvent
{
    public static function detailsUpdated(): self
    {
        return static::create(UserEventType::DetailsUpdated);
    }

    public User $user;
}

abstract class Test
{
    abstract public function begin(): static;

    protected array $field = [];
}

class Test2
{
    public function begin(): static
    {}

    protected array $field = [];
}

abstract class Test3
{
    abstract public function begin(): static|null;

    protected array $field = [];
}

class Test4
{
    public function begin(): static|null
    {}

    protected array $field = [];
}

class Test5
{
    public function begin(): false|static|null
    {}

    protected array $field = [];
}

class Test6
{
    public function begin(): static&null
    {}

    protected array $field = [];
}

class Tes7
{
    public static function begin(): static
    {
        return new static();
    }

    protected array $field = [];

    final private function __construct()
    {
    }
}
