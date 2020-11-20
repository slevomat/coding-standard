<?php // lint >= 8.0

class Whatever
{

	public function getClassWithDifferentParameterThanThis()
	{
		$self = $this;
		return $self::class;
	}

}
