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

	public function __construct(string $a, int|null $b = 0, ?bool $c, $d, $e, string $f)
	{
		$this->a = $a;
		$this->b = $b;
		$this->c = $c;
		$this->d = $d;
		$this->e = $e;
		$this->f = $f;
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
