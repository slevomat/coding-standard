<?php // lint >= 8.0

namespace SomeNamespace;

class Anything
{

}

class Whatever
{

	const FIRST_CONSTANT = 'first';
	const SECOND_CONSTANT = self::FIRST_CONSTANT;

	private $property = self::SECOND_CONSTANT;

	private $arrayProperty = [
		self::FIRST_CONSTANT,
		self::SECOND_CONSTANT,
	];

	#[Attribute(self::FIRST_CONSTANT)]
	public function doSomething(self $parameter): self
	{
		$arrowFunction = fn ($a = self::SECOND_CONSTANT): ?self => null;

		return function (): self|Anything {
		};
	}

}
