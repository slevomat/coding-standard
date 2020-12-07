<?php // lint >= 8.0

class Whatever
{

	public string $a;

	/**
	 * Description
	 *
	 * @var int|null
	 */
	protected int|null $b = null;

	private ?bool $c = null;

	public function __construct(string $a, int|null $b = 0, ?bool $c)
	{
		$this->a = $a;
		$this->b = $b;
		$this->c = $c;
	}

}
