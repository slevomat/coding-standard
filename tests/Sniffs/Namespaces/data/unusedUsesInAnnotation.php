<?php

namespace Foo;

use Assert;
use Doctrine\ORM\Mapping as ORM;
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
	 * @Assert
	 * @Assert\NotBlank(groups={X::SOME_CONSTANT}
	 */
	public function foo()
	{
	}

}
