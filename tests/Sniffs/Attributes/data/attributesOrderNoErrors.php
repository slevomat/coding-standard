<?php

#[Attribute1]
#[Attribute2]
#[\Group\Attribute1]
#[\Group\Attribute2]
#[\Group\Unknown]
#[UnknownOrder]
class Whatever
{

	#[Attribute1] #[Attribute2]
	#[\Group\Attribute1] #[\Group\Attribute2] #[\Group\Unknown]
	#[UnknownOrder]
	public function method()
	{
	}

}

