<?php

namespace Foo;

use Assert;
use Doctrine\ORM\Mapping as ORM;
use Foo\Bar;
use X;
use XX;

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
	 * @Assert\NotBlank(groups={X::SOME_CONSTANT}
	 */
	public function foo()
	{
	}

}
