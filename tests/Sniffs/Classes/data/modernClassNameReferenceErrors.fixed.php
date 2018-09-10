<?php

class Anything
{

}

final class Whatever extends Anything
{
	public function magicConstant()
	{
		return self::class;
	}

	public function getClassWithoutArguments()
	{
		return self::class;
	}

	public function getClassWithThis()
	{
		return static::class;
	}

	public function getParentClass()
	{
		return parent::class;
	}

	public function getParentClassWithThis()
	{
		return parent::class;
	}

	public function getCalledClass()
	{
		return static::class;
	}

	public function getMethodWithFullyQualifiedName()
	{
		return static::class;
	}

}
