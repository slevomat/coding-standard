<?php // lint >= 8.0

namespace FooNamespace;

use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use UsedNamespace\UsedNameFooBar as UsedNameFooBarBaz;
use function DI\string;
use function doSomething;
use function stream_wrapper_restore;
use const STR_PAD_RIGHT;
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

function ($barcodeSettings) {
	if ($barcodeSettings?->getType()) {
		return true;
	}

	return false;
};

class PropertyPromotion
{

	public function __construct(private DateTimeImmutable $dateTimeImmutable, public DateTime $dateTime)
	{
	}

}

class WithUnion
{

	private UnionType|\Union\UnionType2|bool $union;
	private bool|UnionType3 $union2;

	public function call(\Union\UnionType4|UnionType5|bool $union)
	{
	}

	public function call2(bool|UnionType6 $union)
	{
	}

}

echo str_pad('123', 1, pad_type: STR_PAD_RIGHT);
