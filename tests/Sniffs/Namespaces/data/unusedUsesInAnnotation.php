<?php

namespace Foo;

use Assert;
use Doctrine\ORM\Mapping as ORM;
use Foo\Bar;
use X;
use XX;
use XXX;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Property;
use ProxyManager\Proxy\GhostObjectInterface;
use InvalidArgumentException;
use LengthException;
use RuntimeException;
use Symfony\Component\Validator\Constraints as Assert2;
use Foo\Boo\A;
use Foo\Boo\B;
use Foo\Boo\C;
use Foo\Boo\D;
use InvalidAnnotation;
use PropertyAnnotation;
use PropertyReadAnnotation;
use PropertyWriteAnnotation;
use VarAnnotation;
use ParamAnnotation;
use ReturnAnnotation;
use ThrowsAnnotation;
use MethodAnnotation;
use MethodParameter1;
use MethodParameter2;
use MethodParameter3;
use MethodParameter4;
use Discriminator\Lorem;
use Discriminator\Ipsum;
use Outer;
use Inner1;
use Inner2;
use Inner3;
use SeeWithMethod;
use SeeWithoutMethod;
use MethodReturn;
use MethodReturnCollection;
use MethodParameter5;
use Iag1;
use Iag2;
use Iag3;
use Iag4;
use Iag5;
use Iag6;
use Iag7;
use Iag8;
use Iag9;
use Iag10;
use Iag11;
use Iag12;
use Iag13;
use Iag14;
use Callable1;
use Callable2;
use ArrayShape1;
use ArrayShape2;
use ConstantExpression1;
use ConstantExpression2;
use ConstantExpression3;

/**
 * @ORM\Entity()
 * @ORM\DiscriminatorMap({
 *     "lorem" = Lorem::class,
 *     "ipsum" = Ipsum::class,
 * })
 */
class Boo
{

	/**
	 * @ORM\Id()
	 */
	public $id;

	/**
	 * @ORM\OneToMany(targetEntity=Bar::class, mappedBy="boo")
	 * @var \Foo\Bar[]
	 */
	private $bars;

	/**
	 * @Assert
	 * @Assert\NotBlank(groups={X::SOME_CONSTANT})
	 */
	public function foo()
	{
		/** @var XXX\UsedClass() $usedClass */
	}

	/**
	 * @param iterable|Property[] $propertyMappings
	 * @param array|Collection|object[] $collection The collection.
	 * @return object|GhostObjectInterface|null The entity reference.
	 */
	public function bar($propertyMappings, $collection) {}

	/**
	 * @expectedException InvalidArgumentException
	 * @expectedException LengthException
	 * @expectedException RuntimeException
	*/
	public function test()
	{

	}

}

/**
 * @Validate(fields={
 *     "widgetUuid"   = @Assert2\Uuid(),
 *     "clientAuthKey" = @Assert2\NotBlank()
 * })
 */

/**
 * @CustomAnnotation(A::class)
 * @CustomAnnotation(prop=B::class)
 * @CustomAnnotation(@AnotherCustomAnnotation(C::class))
 * @CustomAnnotation(prop=@AnotherCustomAnnotation(D::class))
 */

/**
 * @Outer({
 *     @Inner1(type="string"),
 *     @Inner2,
 *     @Inner3(),
 * })
 */

/** @var $variable InvalidAnnotation */

/**
 * @property PropertyAnnotation $property propertyAnnotation description
 * @property-read PropertyReadAnnotation $propertyRead propertyReadAnnotation description
 * @property-write PropertyWriteAnnotation $propertyWrite propertyWriteAnnotation description
 * @method MethodAnnotation method (MethodParameter1 $m, MethodParameter2 ...$m2) methodAnnotationDescription
 * @method method(MethodParameter3 $m = null, ?MethodParameter4 $m2, $m3) methodAnnotationDescription
 * @method MethodReturn[]|MethodReturnCollection method(MethodParameter5 $m)
 */
class Foo
{

	/** @var VarAnnotation varAnnotation description */
	private $varAnnotation;

	/**
	 * @param ParamAnnotation $paramAnnotation paramAnnotation description
	 * @return ReturnAnnotation returnAnnotation description
	 * @throws ThrowsAnnotation throwsAnnotation description
	 */
	public function method($paramAnnotation)
	{
		return null;
	}

	/**
	 * @see SeeWithMethod::method()
	 * @see SeeWithoutMethod
	 */
	public function see()
	{

	}

	/**
	 * @return $this
	 */
	public function returnThis()
	{
		return $this;
	}

}

/**
 * @method Iag12|array method3(?Iag13<Iag14> $m)
 * @property Iag1&Iag2 $property
 */
class IntersectionAndGeneric
{

	/** @var (Iag3|null|\Foo\DateTime)[] */
	public $a;

	/** @var Iag4&\Traversable<int, Iag5> */
	public $b;

	/** @var string&(\DateTimeImmutable|Iag6) */
	public $c;

	/** @var string|(Iag7&\Foo\DateTime) */
	public $d;

	/** @var Iag8[][][] */
	public $e;

	/** @var (Iag9|\Foo\DateTime)[][][] */
	public $f;

	/** @var int|(Iag10<string>&Iag11)[] */
	public $g;

}

class CallableType
{

	/**
	 * @return callable(Callable1 $bool): Callable2
	 */
	public function returnsCallable()
	{

	}

}

/** @var array{int, ArrayShape1} $arrayShape1 */
$arrayShape1 = [];

/** @var array{int: ArrayShape2} $arrayShape2 */
$arrayShape2 = [];

/**
 * @method method1(string $parameter = ConstantExpression1::class)
 * @method method2(array $parameter = [ConstantExpression2::class => ConstantExpression3::class])
 */
class ConstantExpression
{

}
