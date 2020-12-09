<?php // lint >= 8.0

namespace DontKnow;

use Something;
use Nothing;
use Whatever\Anything;
use const PHP_VERSION;
use Doctrine\Column;
use Nette\DI\Attributes\Inject;

#[Something(Nothing::TARGET_CLASS | Anything::IS_REPEATABLE, PHP_VERSION, parameter1: 123, parameter2: [Nothing::SOMETHING, 'string'])]
class WithAttribute
{

	#[Column(
		type: 'string',
		length: 32,
		unique: true,
		properties: [Column::NULLABLE],
	)]
	#[Inject]
	private $column;

}
