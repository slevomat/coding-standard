<?php

#[UnknownOrder]
#[\Group\Unknown]
#[\Group\Attribute2]
#[Attribute2]
#[\Group\Attribute1]
#[Attribute1]
class Whatever
{

	#[UnknownOrder] #[\Group\Unknown] #[\Group\Attribute2] #[Attribute2] #[\Group\Attribute1] #[Attribute1]
	public function method()
	{
	}

	#[UnknownOrder] #[\Group\Unknown]
	#[\Group\Attribute2] #[Attribute2] #[\Group\Attribute1]
	#[Attribute1]
	public function method2()
	{
	}

}

