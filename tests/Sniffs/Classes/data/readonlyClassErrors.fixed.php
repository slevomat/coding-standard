<?php // lint >= 8.2

readonly class Candidate
{
    public function __construct(
		private int $id,
		public string $name,
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

readonly class WithBodyPropertiesCandidate
{
    private int $id;
    public string $name;
}

readonly class MixedPropertiesCandidate
{
    private string $extra;

    public function __construct(private int $id)
    {
    }
}

readonly class WithoutPromotion
{
    private int $id;
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
