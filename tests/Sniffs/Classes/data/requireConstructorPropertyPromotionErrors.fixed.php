<?php // lint >= 8.1

class Whatever
{

	#[SomeAttribute]
	private $d;

	public function __construct(public string $a, protected int|null $b = 0, private ?bool $c = null, $d, private $e, private readonly string $f)
	{
		$this->d = $d;
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
