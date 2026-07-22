<?php // lint >= 8.2

trait SomeTrait
{
	public function process(): void
	{
	}
}

readonly class NonFinalCandidate
{
	public function __construct(private int $id)
	{
	}
}

final readonly class FinalWithTraitCandidate
{
	use SomeTrait;

	public function __construct(private int $id)
	{
	}
}

readonly class NonFinalWithTraitCandidate
{
	use SomeTrait;

	public function __construct(private int $id)
	{
	}
}

abstract class AbstractWithPromotedProperties
{
	public function __construct(private readonly int $id)
	{
	}
}

abstract class AbstractWithBodyProperties
{
	private readonly int $id;
}
