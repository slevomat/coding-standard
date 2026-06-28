<?php // lint >= 8.2

class MixedPromotion
{
    public function __construct(private readonly int $id, private string $name,)
    {
    }
}

readonly class ValidReadonly
{
    public function __construct(private int $id, private string $name,)
    {
    }
}

class PromotedReadonlyWithNonReadonlyBodyProperty
{
    private int $extra;

    public function __construct(private readonly int $id)
    {
    }
}

class ChildClassWithAllReadonlyProperties extends ParentClass
{
    public function __construct(private readonly int $id, private readonly string $name)
    {
    }
}

class ChildClassWithReadonlyBodyProperties extends ParentClass
{
    private readonly int $id;
    public readonly string $name;
}

trait TraitWithMutableProperty
{
	public int $traitProperty;
}

final class FinalClassUsingTrait
{
	use TraitWithMutableProperty;

	public function __construct(private readonly int $id)
	{
	}
}

#[\AllowDynamicProperties]
final class FinalClassWithAllowDynamicProperties
{
	public function __construct(private readonly int $id)
	{
	}
}
