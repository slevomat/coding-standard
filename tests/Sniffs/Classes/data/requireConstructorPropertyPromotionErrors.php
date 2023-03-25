<?php // lint >= 8.1

class Whatever
{

	public string $a;

	/**
	 * @var int|null
	 */
	protected int|null $b = null;

	private ?bool $c = null;

	#[SomeAttribute]
	private $d;

	private $e;

	private readonly string $f;

    /** @phpstan-var array<int, string> */
	private array $g;

    /** @phpstan-param array<int, string> $g */
	public function __construct(string $a, int|null $b = 0, ?bool $c, $d, $e, string $f, array $g)
	{
		$this->a = $a;
		$this->b = $b;
		$this->c = $c;
		$this->d = $d;
		$this->e = $e;
		$this->f = $f;
		$this->g = $g;
	}

}

class DontKnow
{

	private bool $active;

	private ?DateTimeImmutable $from = null;

	public function __construct(bool $active = false, ?DateTimeImmutable $from = null)
	{
		if ($from !== null) {
			$active = true;
		}
		$this->active = $active;
		$this->from = $from;
	}

}
