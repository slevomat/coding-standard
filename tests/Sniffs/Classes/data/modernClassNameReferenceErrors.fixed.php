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

	public function getClassWithoutArgumentsWithFullyQualifiedName()
	{
		return self::class;
	}

	public function getClassWithThisWithFullyQualifiedName()
	{
		return static::class;
	}

	public function getParentClassWithFullyQualifiedName()
	{
		return parent::class;
	}

	public function getParentClassWithThisWithFullyQualifiedName()
	{
		return parent::class;
	}

	public function getCalledClassWithFullyQualifiedName()
	{
		return static::class;
	}

}
