<?php // lint >= 8.0

namespace DontKnow;

use Something;
use Nothing;

#[Something(Nothing::TARGET_CLASS | \Whatever\Anything::IS_REPEATABLE, \PHP_VERSION, parameter1: 123, parameter2: [Nothing::SOMETHING, 'string'])]
class WithAttribute
{

	#[\Doctrine\Column(
		type: 'string',
		length: 32,
		unique: true,
		properties: [\Doctrine\Column::NULLABLE],
	)]
	#[\Nette\DI\Attributes\Inject]
	private $column;

}
