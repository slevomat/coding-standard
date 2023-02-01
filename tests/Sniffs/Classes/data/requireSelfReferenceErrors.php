<?php // lint >= 8.0

namespace SomeNamespace;

class Anything
{

}

class Whatever
{

	const FIRST_CONSTANT = 'first';
	const SECOND_CONSTANT = \SomeNamespace\Whatever::FIRST_CONSTANT;

	private $property = Whatever::SECOND_CONSTANT;

	private $arrayProperty = [
		Whatever::FIRST_CONSTANT,
		\SomeNamespace\Whatever::SECOND_CONSTANT,
	];

	#[Attribute(Whatever::FIRST_CONSTANT)]
	public function doSomething(\SomeNamespace\Whatever $parameter): Whatever
	{
		$arrowFunction = fn ($a = Whatever::SECOND_CONSTANT): ?Whatever => null;

		return function (): Whatever|Anything {
		};
	}

}
