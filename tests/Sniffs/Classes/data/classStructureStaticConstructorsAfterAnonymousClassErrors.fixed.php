<?php

class WithAnonymousClass
{

	public function method()
	{
		return new class {
		};
	}

	public static function createWithAnonymousClass(): WithAnonymousClass
	{
	}

}

class WithoutAnonymousClass
{

	public static function createWithoutAnonymousClass(): WithoutAnonymousClass
	{
	}

	public function method()
	{
	}

}
