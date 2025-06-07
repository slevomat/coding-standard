<?php // lint >= 8.4

abstract class Test {
	abstract public function __construct($x);
}

interface Test {
	public function __construct($x);
}

class Whatever
{

	/**
	 * No parameters.
	 */
	public function __construct()
	{
	}

	public function noConstructor()
	{
	}

}

class Something
{

	public function __construct($noProperty)
	{
	}

}

class Anything
{

	public function __construct(public $withPromotion, private string $withPromotionAndTypeHint, callable &$callable, ...$variadic)
	{
	}

}

class Nothing
{

	private $a = '';

	private $b;

	private $c;

	private $d;

	/** @ORM\Column(type="string") */
	private string $e;

	/** Description */
	private string $f;

	/** @var string Description */
	private $g;

	/**
	 * @var string
	 * @phpstan-var class-string
	 */
	private string $h;

	private ?string $i;

	private $j;

	#[SomeAttribute]
	private $k;

	private int $l;
	private int $m;

	public function __construct($a, $c, $d, $e, $f, $g, $h, string $i, string $j, $k, int $l, int $m)
	{
		$phpVersion = phpversion();

		$className = $this::class;

		$this->b = 'b';

		$this->a .= 'a';

		$this->c = $a;

		$this->d = $d + $c;

		$this->e = $e;

		$this->f = $f;

		$this->g = $g;

		$this->h = $h;

		$this->i = $i;

		$this->j = $j;

		$this->k = $k;

		$l++;
		$this->l = $l;

		--$m;
		$this->m = $m;
	}

}

class Foo
{

	private SimpleXMLElement|string|null $openingHours = null;

	private ?string $formattedOpeningHours;

	public function __construct(SimpleXMLElement|string|null $openingHours)
	{
		if ($openingHours instanceof SimpleXMLElement) {
			$this->openingHours = $openingHours;
		} elseif ($openingHours === null) {
			$this->openingHours = null;
		} else {
			$this->formattedOpeningHours = $openingHours;
		}
	}

}

class Hooks
{

	public string $email {
		get => 'mailto:' . $this->email;
		set (string $value) {
			$this->email = $value;
		}
	}


	public function __construct(string $email)
	{
		$this->email = $email;
	}

}
