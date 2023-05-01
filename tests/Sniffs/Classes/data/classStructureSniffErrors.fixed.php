<?php // lint >= 8.1

class A
{
	use SomeTrait;
	const LOREM = 1;
}

class B
{
	const IPSUM = 1;
	private const LOREM = 1;
}

class C
{
	private const LOREM = 1;
	private static $lorem;
}

class D
{
	protected $lorem;
	private static $ipsum;
}

class E
{
	public function __construct()
	{
	}

	static function staticLorem()
	{
	}
}

class F
{
	protected static function staticDolor()
	{
	}

	private function __call($name, $arguments)
	{
	}
}

class G
{
	public function __construct()
	{
	}

	public function __get($name)
	{
	}
}

class H
{
	private function sit()
	{
	}

	private function __call($name, $arguments)
	{
	}
}

class I
{
	protected static function staticDolor()
	{
	}

	private function sit()
	{
	}
}

class J
{
	protected static function staticDolor()
	{
	}

	private static function staticSit()
	{
	}
}

class K
{
	function lorem()
	{
	}

	private function sit()
	{
	}
}

class L
{
	function lorem()
	{
		new class()
		{
			const IPSUM = 1;
			private const LOREM = 1;

			function ipsum()
			{
			}

			private function lorem()
			{
			}
		};
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
	private int $sit;

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

class M
{
	private function __construct()
	{
	}

	/**
	 * Static constructor
	 *
	 * @return static
	 */
	public static function staticConstructorM()
	{
	}

	public static function notAStaticConstructor() : D
	{
	}

	public static function notAStaticConstructorA()
	{
	}

	public static function notAStaticConstructorB() : D
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

	abstract protected static function abstractStaticMethod();

	abstract public function abstractMethod();

	public function notAbstractMethod()
	{
	}

}

class N
{

	public $whatever;

	/**
	* The attributes that should be hidden for arrays.
	*
	* @var array
	*/
	protected $hidden = [
		'password',
		'remember_token',
	];

}

enum O: string
{

	case SOME_ENUM_CASE = 'case';
	const SOME_CONST = 'const';

}

class Foo
{
	use SettingsTrait {
		SettingsTrait::__construct as private __settingsConstruct;
	}
	private const BA = 582000;
	private const KE = 599981;
}
