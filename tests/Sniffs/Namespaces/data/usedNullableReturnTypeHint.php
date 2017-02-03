<?php // lint >= 7.1

declare(strict_types = 1);

namespace Driveto\AppBundle\Catalog;

use DateTimeImmutable;

class Foo
{

	public function getDeleted($entity): ?DateTimeImmutable
	{
		return $entity->getDeleted();
	}

}
