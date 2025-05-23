<?php // lint >= 8.1

class Whatever
{

	#[SomeAttribute]
	private $d;

    /** @phpstan-param array<int, string> $g */
	public function __construct(public string $a, protected int|null $b = 0, private ?bool $c = null, $d, private $e, private readonly string $f, private array $g)
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

class Credentials
{

	public function __construct(
		#[\SensitiveParameter]
		private string $login,
		#[\SensitiveParameter]
		private string $password
	) {
	}

}
