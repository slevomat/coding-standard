<?php // lint >= 8.4

abstract class Foo {
	/** @var string */
	public static $staticFoo = 'bar'; // there may be a comment
	/** @var string */
	protected  static $staticBar = 'foo';
	/** @var int */
	private static $staticLvl = 666;
	// strange but yeah, whatever
	public $foo = 'bar';
	/** @var string */
	protected  $bar = 'foo';
	/** @var int */
	private $lvl = 9001;
	/**
	 * whatever
	 */
	public static abstract function wow();
	/**
	 * who cares
	 */
	private function such()
	{
	}
}

abstract class Bar {
	/** @var string */
	public static $staticFoo = 'bar'; // there may be a comment


	/** @var string */
	protected  static $staticBar = 'foo';



	/** @var int */
	private static $staticLvl = 666;


	// strange but yeah, whatever
	public $foo = 'bar';




	/** @var string */
	protected  $bar = 'foo';


	/** @var int */
	private $lvl = 9001;


	private $noComment = 'noComment';


	private int $int = 0;


	private string $string = '';


	readonly string $readonly;


	static $static = 'static';


	private readonly string $privateReadonly;

	/**
	 * whatever
	 */
	public static abstract function wow();
	/**
	 * who cares
	 */
	private function such()
	{
	}
}

abstract class Something
{

	public final int $publicFinal = 0;



	readonly private(set) public ?string $readonlyPublicPrivateSet;


	static protected array $staticProtected = [];


	public string $email {
		get => 'mailto:' . $this->email;
		set (string $value) {
			$this->email = $value;
		}
	}

}
