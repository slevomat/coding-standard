<?php // lint >= 8.0

#[Something(Anything::TARGET_CLASS | \Whatever\Anything::IS_REPEATABLE, PHP_VERSION, parameter1: 123, parameter2: [Nothing::SOMETHING, 'string'])]
final class AttributeOverride
{
}

# Comment

class WithAttribute
{

	#[\Doctrine\Column(
		type: 'string',
		length: 32,
		unique: true,
		properties: [\Doctrine\Column\Properties::NULLABLE],
	)]
	private $column;

	#[Attribute1, Attribute2]
	#[\Nette\DI\Attributes\Inject]
	private $moreAttributes;
}
