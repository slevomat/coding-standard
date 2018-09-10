<?php

class Anything
{

}

final class Whatever extends Anything
{
	public function magicConstant()
	{
		return __CLASS__;
	}

	public function getClassWithoutArguments()
	{
		return get_class();
	}

	public function getClassWithThis()
	{
		return get_class($this);
	}

	public function getParentClass()
	{
		return get_parent_class();
	}

	public function getParentClassWithThis()
	{
		return get_parent_class($this);
	}

	public function getCalledClass()
	{
		return get_called_class();
	}

	public function getMethodWithFullyQualifiedName()
	{
		return \get_called_class();
	}

}
