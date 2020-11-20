<?php // lint >= 8.0

class Whatever
{

	public function getClassWithDifferentParameterThanThis()
	{
		$self = $this;
		return get_class($self);
	}

}
