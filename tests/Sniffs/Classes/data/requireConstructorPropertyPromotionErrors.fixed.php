<?php // lint >= 8.0

class Whatever
{

	public function __construct(public string $a, protected int|null $b = 0, private ?bool $c = null)
	{
	}

}

class DontKnow
{

	private bool $active;

	public function __construct(bool $active = false, private ?DateTimeImmutable $from = null)
	{
		if ($from !== null) {
			$active = true;
		}
		$this->active = $active;
	}

}
