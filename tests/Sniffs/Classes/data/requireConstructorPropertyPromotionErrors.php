<?php // lint >= 8.0

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

	public function __construct(string $a, int|null $b = 0, ?bool $c, $d, $e)
	{
		$this->a = $a;
		$this->b = $b;
		$this->c = $c;
		$this->d = $d;
		$this->e = $e;
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
