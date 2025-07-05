<?php

use Group\Unknown;

#[Attribute1]
#[Attribute2]
#[\Group\Attribute1]
#[\Group\Attribute2]
#[\Group\Unknown]
#[AppAssertB\SomeAssert]
#[AppAssertA\SomeAssert]
#[UnknownOrder]
class Whatever
{

	#[Attribute1] #[Attribute2] #[\Group\Attribute1] #[\Group\Attribute2] #[\Group\Unknown] #[UnknownOrder]
	public function method()
	{
	}

	#[Attribute1]
	#[Attribute2]
	#[\Group\Attribute1]
	#[\Group\Attribute2]
	#[Unknown]
	#[\Group\Unknown]
	#[AppAssertB\SomeAssert]
	#[AppAssertA\SomeAssert]
	#[UnknownOrder]
	public function method2()
	{
	}

}

