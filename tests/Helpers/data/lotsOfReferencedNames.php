<?php

namespace FooNamespace;

use Doctrine\ORM\Mapping as ORM;
use UsedNamespace\UsedNameFooBar as UsedNameFooBarBaz;
use function DI\string;
use function doSomething;
use function stream_wrapper_restore;
use const STREAM_URL_STAT_QUIET;

class FooClass extends \ExtendedClass implements \ImplementedInterface, \SecondImplementedInterface, \ThirdImplementedInterface
{

	use \FullyQualified\SomeOtherTrait, SomeDifferentTrait, \FullyQualified\SometTotallyDifferentTrait;
	use SomeTrait;

	/** @ORM\Column(name="foo") */
	private $foo;

	/** @var Bar */
	private $bar;

	/** @var Lorem[]|Ipsum|null */
	private $baz;

	/** @var Rasmus|Lerdorf[]|null|string|self|\Foo\BarBaz */
	private $barz;

	private $boo = 1, $hoo = SomeClass::CLASS_CONSTANT, $doo = TYPE_ONE;

	const ARRAY = [
		ArrayKey1::CONSTANT => true,
		ArrayKey2::CONSTANT => true,
	];

	/**
	 * @param TypeHintedName $foo
	 * @param AnotherTypeHintedName[] $bar
	 * @return Returned_TypeHinted_Underscored_Name
	 */
	public function fooMethod(TypeHintedName $foo, array $bar)
	{
		try {
			$var = new ClassInstance();
			$var->objectMethod();
			StaticClass::staticMethod();
			throw new \Foo\Bar\SpecificException();
		} catch (\Foo\Bar\Baz\SomeOtherException $e) {
			throw $e;
		}

		callToFunction(FOO_CONSTANT);
		$baz = BAZ_CONSTANT;
		$lorem = new LoremClass;
		$ipsum = IpsumClass::IPSUM_CONSTANT;

		$array = [Hoo::HOO_CONSTANT, BAR_CONSTANT];

		new Integer();
		new Boolean();

		function (Bobobo ...$bobobo) : array {
			return $bobobo;
		};

		function (Dododo &$dododo) : array {
			return $dododo;
		};
	}

}

interface FooInterface extends \ExtendedInterface, \SecondExtendedInterface, \ThirdExtendedInterface
{
}

trait FooTrait
{

	use SomeTrait {
		__construct as initTrait;
	}

}

const TYPE_ONE = 1, TYPE_TWO = 2, TYPE_THREE = 3;

final class SomeClass
{

	/** @var SomeClass2 */
	private $someClass2;

	public function work() : string
	{
		return $this->someClass2::get();
	}

}

class OpenSsl
{
	public const LENGTH = [
		OPENSSL_ALGO_SHA256 => 64,
		OPENSSL_ALGO_SHA512 => 132,
	];
}

$class = new class ()
{
	use SomeTrait;
};

string();

function whatever($flags)
{
	foreach ([] as $item) {
		doSomething($item);
	}

	if ($flags & STREAM_URL_STAT_QUIET) {

	}
}

echo E_ALL & ~E_NOTICE;
