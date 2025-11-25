<?php // lint >= 8.4

class Foo
{

	use Whatever {
		doSomething as public;
	}

	private $boo = 'boo';

	public string $booo = 'booo';

	protected array $array = [
		'a',
		'b',
		'c',
	];

	public private(set) LocalizedString $hook {
		get => LocalizedString::fromCzechAndSlovak($this->locativeCs, $this->locativeSk);
		set {
			$this->locativeCs = $value->getInCzech(required: false);
			$this->locativeSk = $value->getInSlovak(required: false);
		}
	}

	public $oldArray = array(
		'a',
		'b',
	);

	public function __construct(private string $propertyPromotion, private int $propertyPromotion2)
	{
	}

	public function anything()
	{

	}

}
