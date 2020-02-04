<?php // lint >= 7.4

class A
{
}

class B
{
	const LOREM = 1;
}

class C
{
	protected $lorem;
	private $ipsum;
}

class D
{
	use SomeTrait;

	const LOREM = 1;
	public const IPSUM = 1;
	protected const DOLOR = 1;
	private const SIT = 1;

	var string $lorem;
	public $ipsum;
	static int $staticLorem;
	public static $staticIpsum;

	protected $dolor;
	protected static $staticDolor;

	private $sit;
	private static $staticSit;

	private function __construct()
	{
	}

	public static function staticConstructorA() : self
	{
	}

	public static function staticConstructorB() : D
	{
	}

	/**
	 * @return static
	 */
	public static function staticConstructorC()
	{
	}

	function __destruct()
	{
		new class ()
		{
			use SomeTrait;

			const LOREM = 1;
			public const IPSUM = 1;
			protected const DOLOR = 1;
			private const SIT = 1;

			var $lorem;
			public $ipsum;
			static $staticLorem;
			public static $staticIpsum;

			protected $dolor;
			protected static $staticDolor;

			private $sit;
			private static $staticSit;

			private function __construct()
			{
			}

			public static function staticConstructorA() : self
			{
			}

			/**
			 * @return static
			 */
			public static function staticConstructorB()
			{
			}

			function __destruct()
			{

			}

			function lorem() {
			}

			public function ipsum() {
			}

			static function staticLorem() {
			}

			public static function staticIpsum() {
			}

			protected function dolor() {
			}

			protected static function staticDolor() {
			}

			private function sit() {
			}

			private static function staticSit() {
			}

			private function __call($name, $arguments)
			{
			}

			public function __get($name)
			{
			}
		};
	}

	function lorem() {
	}

	public function ipsum() {
	}

	static function staticLorem() {
	}

	public static function staticIpsum() {
	}

	protected function dolor() {
	}

	protected static function staticDolor() {
	}

	private function sit() {
	}

	private static function staticSit() {
	}

	private function __call($name, $arguments)
	{
	}

	public function __get($name)
	{
	}

}

interface Intf
{
	const LOREM = 1;

	function dolor();

	static function staticLorem();

	public static function staticIpsum();
}

trait Tr
{
	use SomeTrait;

	static $staticLorem;

	private $sit;
	private static $staticSit;

	public function __construct()
	{
	}

	function lorem() {
	}

	protected static function staticDolor() {
	}

	private function sit() {
	}

	private static function staticSit() {
	}

	private function __call($name, $arguments)
	{
	}

}

abstract class WithAbstract
{

	abstract protected static function abstractStaticMethod();

	abstract public function abstractMethod();

	public function notAbtractMethod()
	{
	}

}
