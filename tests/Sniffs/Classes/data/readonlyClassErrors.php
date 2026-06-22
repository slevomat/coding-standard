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
		private readonly int $id,
		protected readonly string $name,
	)
    {
    }
}

final class FinalCandidate
{
    public function __construct(private readonly int $id)
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
    private readonly int $extra;

    public function __construct(private int $id)
    {
    }
}
