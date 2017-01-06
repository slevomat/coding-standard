<?php

class ClassWithSpecialThis
{

	public $publicProperty;

	public $publicProperty2;

	private $property;

	public function specialThis()
	{
		$this->{'publicProperty'} = $this->publicProperty2;
		$this->property = $this;
	}

	public function returnProperty()
	{
		return $this->property;
	}

}
