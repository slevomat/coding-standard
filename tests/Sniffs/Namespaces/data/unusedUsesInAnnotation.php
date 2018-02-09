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

/**
 * @ORM\Entity()
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
