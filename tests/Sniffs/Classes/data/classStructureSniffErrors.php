<?php // lint >= 7.4

class A
{
	const LOREM = 1;
	use SomeTrait;
}

class B
{
	private const LOREM = 1;
	const IPSUM = 1;
}

class C
{
	private static $lorem;
	private const LOREM = 1;
}

class D
{
	private static $ipsum;
	protected $lorem;
}

class E
{
	static function staticLorem()
	{
	}

	public function __construct()
	{
	}
}

class F
{
	private function __call($name, $arguments)
	{
	}

	protected static function staticDolor()
	{
	}
}

class G
{
	public function __get($name)
	{
	}

	public function __construct()
	{
	}
}

class H
{
	private function __call($name, $arguments)
	{
	}

	private function sit()
	{
	}
}

class I
{
	private function sit()
	{
	}

	protected static function staticDolor()
	{
	}
}

class J
{
	private static function staticSit()
	{
	}

	protected static function staticDolor()
	{
	}
}

class K
{
	private function sit()
	{
	}

	function lorem()
	{
	}
}

class L
{
	function lorem()
	{
		new class()
		{
			private const LOREM = 1;
			const IPSUM = 1;

			private function lorem()
			{
			}

			function ipsum()
			{
			}
		};
	}
}

interface Intf
{
	static function staticLorem();

	const LOREM = 1;

	public static function staticIpsum();

	function dolor();
}

trait Tr
{
	use SomeTrait;

	private static $staticSit;
	static $staticLorem;

	private static function staticSit() {
	}

	private int $sit;

	protected static function staticDolor() {
	}

	private function __call($name, $arguments)
	{
	}

	public function __construct()
	{
	}

	private function sit() {
	}

	function lorem() {
	}
}

class M
{
	public static function notAStaticConstructor() : D
	{
	}

	public static function notAStaticConstructorA()
	{
	}

	private function __construct()
	{
	}

	public static function notAStaticConstructorB() : D
	{
	}

	/**
	 * Static constuctor
	 *
	 * @return static
	 */
	public static function staticConstructorM()
	{
	}

	/**
	 * @return D
	 */
	public static function notAStaticConstructorC()
	{
	}
}

abstract class WithAbstract
{

	public function notAbtractMethod()
	{
	}

	abstract public function abstractMethod();

	abstract protected static function abstractStaticMethod();

}

class N
{

	/**
	* The attributes that should be hidden for arrays.
	*
	* @var array
	*/
	protected $hidden = [
		'password',
		'remember_token',
	];

	public $whatever;

}
