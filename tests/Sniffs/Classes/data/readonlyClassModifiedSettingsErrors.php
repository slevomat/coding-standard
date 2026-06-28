<?php // lint >= 8.2

trait SomeTrait
{
	public function process(): void
	{
	}
}

class NonFinalCandidate
{
	public function __construct(private readonly int $id)
	{
	}
}

final class FinalWithTraitCandidate
{
	use SomeTrait;

	public function __construct(private readonly int $id)
	{
	}
}

class NonFinalWithTraitCandidate
{
	use SomeTrait;

	public function __construct(private readonly int $id)
	{
	}
}
