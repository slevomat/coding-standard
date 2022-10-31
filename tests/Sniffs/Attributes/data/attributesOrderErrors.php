<?php

#[UnknownOrder]
#[\Group\Unknown]
#[\Group\Attribute2]
#[Attribute2]
#[AppAssertB\SomeAssert]
#[\Group\Attribute1]
#[Attribute1]
#[AppAssertA\SomeAssert]
class Whatever
{

	#[UnknownOrder] #[\Group\Unknown] #[\Group\Attribute2] #[Attribute2] #[\Group\Attribute1] #[Attribute1]
	public function method()
	{
	}

	#[UnknownOrder] #[\Group\Unknown] #[AppAssertB\SomeAssert]
	#[\Group\Attribute2] #[Attribute2] #[\Group\Attribute1]
	#[Attribute1] #[AppAssertA\SomeAssert]
	public function method2()
	{
	}

}

