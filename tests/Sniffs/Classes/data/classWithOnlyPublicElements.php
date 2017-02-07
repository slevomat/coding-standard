<?php

class ClassWithOnlyPublicElements
{

	public $publicProperty;

	public function foo()
	{
		$this->usedProperty->foo();
		$this->usedPrivateMethod();
	}

	public function publicMethod()
	{

	}

	public static function staticPublicMethod()
	{
		self::usedStaticPrivateMethod();
	}

}
