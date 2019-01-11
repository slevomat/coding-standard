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

}

