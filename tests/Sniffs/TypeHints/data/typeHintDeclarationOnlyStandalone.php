<?php

class StandaloneClass
{

	public $property;

	public function method($parameter)
	{
	}

}

interface StandaloneInterface
{

	public function method($parameter);

}

trait StandaloneTrait
{

	public $property;

	public function method($parameter)
	{
	}

}

class ExtendingClass extends StandaloneClass
{

	public $property;

	public function method($parameter)
	{
	}

}

class ImplementingClass implements StandaloneInterface
{

	public $property;

	public function method($parameter)
	{
	}

}

class UsingClass
{
	use StandaloneTrait;

	public $property;

	public function method($parameter)
	{
	}

}

interface ExtendingInterface extends StandaloneInterface
{

	public function method($parameter);

}

trait UsingTrait
{
	use StandaloneTrait;

	public $property;

	public function method($parameter)
	{
	}

}

function someFunction($parameter)
{
}

$someClosure = function () {
};
