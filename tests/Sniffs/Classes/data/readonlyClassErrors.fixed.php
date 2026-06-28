<?php // lint >= 8.2

class Candidate
{
    public function __construct(
		private readonly int $id,
		public readonly string $name,
	)
    {
    }
}

readonly class InvalidReadonly
{
    public function __construct(
		private int $id,
		protected string $name,
	)
    {
    }
}

final readonly class FinalCandidate
{
    public function __construct(private int $id)
    {
    }
}

class WithBodyPropertiesCandidate
{
    private readonly int $id;
    public readonly string $name;
}

class MixedPropertiesCandidate
{
    private readonly string $extra;

    public function __construct(private readonly int $id)
    {
    }
}

class WithoutPromotion
{
    private readonly int $id;
    public function __construct(int $id)
    {
        $this->id = $id;
    }
}

readonly class InvalidReadonlyWithBodyProperty
{
    private int $extra;

    public function __construct(private int $id)
    {
    }
}

readonly class InvalidReadonlyWithBodyProperty extends InvalidReadonly
{
	private int $extra;

	public function __construct(private int $id)
	{
	}
}

final class FinalExtendingCandidate extends ParentClass
{
	public function __construct(private readonly int $id, private readonly string $name)
	{
	}
}

final readonly class FinalWithBodyPropertiesCandidate
{
	private int $id;
	public string $name;
}
