<?php

use Group\Unknown;

#[Attribute1]
#[Attribute2]
#[\Group\Attribute1]
#[\Group\Attribute2]
#[\Group\Unknown]
#[AppAssertA\SomeAssert]
#[AppAssertB\SomeAssert]
#[UnknownOrder]
class Whatever
{

	#[Attribute1] #[Attribute2]
	#[\Group\Attribute1] #[\Group\Attribute2] #[\Group\Unknown] #[Unknown]
	#[AppAssertA\SomeAssert] #[AppAssertB\SomeAssert]
	#[UnknownOrder]
	public function method()
	{
	}

}

