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

	public function method()
	{
	}

	public static function createWithoutAnonymousClass(): WithoutAnonymousClass
	{
	}

}
