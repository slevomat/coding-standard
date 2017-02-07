<?php

class ClassWithSpecialSelf
{

	const CONSTANT = 0;

	const CONSTANT2 = 0;

	private $property;

	public function specialSelf()
	{
		self::${'CONSTANT'} = self::CONSTANT2;
		$this->property = self::CONSTANT;
	}

	public function returnProperty()
	{
		return $this->property;
	}

}
